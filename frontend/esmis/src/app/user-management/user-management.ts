import { Component, inject, signal, effect, computed } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormArray, FormBuilder, FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { debounceTime, distinctUntilChanged } from 'rxjs';
import { User, Office, Role, Permission } from '../../models/smis.model';
import { UserManagementService } from '../../services/user-management.service';
import { AuthService } from '../../services/auth.service';
import { ToastService } from '../../services/toast.service';
import { ConfirmService } from '../../services/confirm.service';

@Component({
  selector: 'app-user-management',
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './user-management.html',
  styleUrl: './user-management.scss',
})
export class UserManagement {
  userService = inject(UserManagementService);
  formBuilder = inject(FormBuilder);
  authService = inject(AuthService);
  toastService = inject(ToastService);
  confirmService = inject(ConfirmService);

  users = signal<User[]>([]);
  isLoading = signal(false);
  currentPage = signal(1);
  lastPage = signal(1);
  totalUsers = signal(0);
  editMode = signal(false);
  activeUser = signal<User | null>(null);
  isProcessingBackup = signal(false);
  isProcessingRestore = signal(false);
  restoreFile: File | null = null;
  restorePassword = signal('');
  showRestorePassword = signal(false);

  searchControl = new FormControl('');

  userForm: FormGroup = this.formBuilder.group({
    first_name: ['', Validators.required],
    middle_initial: ['', Validators.maxLength(1)],
    last_name: ['', Validators.required],
    email: ['', [Validators.required, Validators.email]],
    role_id: [null, Validators.required],
    office_name: ['', Validators.required],
    permission_ids: [[]],
    override_permissions: [false],
  });

  roleOptions: Role[] = [];
  officeOptions: Office[] = [];
  allPermissions: Permission[] = [];
  
  permissionGroups: { label: string, permissions: { name: string, label: string }[] }[] = [
    {
      label: 'Requests',
      permissions: [
        { name: 'view_pending_requests', label: 'View Pending' },
        { name: 'view_approved_requests', label: 'View Approved' },
        { name: 'view_released_requests', label: 'View Released' },
        { name: 'view_disapproved_requests', label: 'View Disapproved' },
        { name: 'edit_ris', label: 'Edit RIS' },
        { name: 'deny_request', label: 'Deny' },
      ]
    },
    {
      label: 'Users',
      permissions: [
        { name: 'add_user', label: 'Add User' },
        { name: 'edit_user', label: 'Edit User' },
        { name: 'delete_user', label: 'Delete User' },
      ]
    },
    {
      label: 'Supply',
      permissions: [
        { name: 'add_supply', label: 'Add Supply' },
        { name: 'edit_supply', label: 'Edit Supply' },
        { name: 'delete_supply', label: 'Delete Supply' },
      ]
    },
    {
      label: 'Categories',
      permissions: [
        { name: 'add_category', label: 'Add Category' },
        { name: 'delete_category', label: 'Delete Category' },
      ]
    },
    {
      label: 'Units',
      permissions: [
        { name: 'add_unit', label: 'Add Unit' },
        { name: 'delete_unit', label: 'Delete Unit' },
      ]
    }
  ];

  constructor() {
    this.loadUsers();
    this.loadOffices();
    this.loadRoles();
    this.loadPermissions();

    // Setup reactive debounced auto-search
    this.searchControl.valueChanges.pipe(
      debounceTime(300),
      distinctUntilChanged()
    ).subscribe(() => {
      this.loadUsers(1);
    });

    // Sync default permissions or filter custom permissions when role_id changes
    this.userForm.get('role_id')?.valueChanges.subscribe(roleId => {
      if (!roleId) {
        this.userForm.patchValue({ permission_ids: [] });
      } else {
        const numericRoleId = Number(roleId);
        if (!this.userForm.get('override_permissions')?.value) {
          this.updatePermissionsByRole(numericRoleId);
        } else {
          this.filterPermissionsByRoleVisibility(numericRoleId);
        }
      }
    });

    // Revert to defaults or filter when override_permissions is toggled
    this.userForm.get('override_permissions')?.valueChanges.subscribe(override => {
      const roleIdValue = this.userForm.get('role_id')?.value;
      if (!roleIdValue) return;

      const numericRoleId = Number(roleIdValue);
      if (!override) {
        this.updatePermissionsByRole(numericRoleId);
      } else {
        this.filterPermissionsByRoleVisibility(numericRoleId);
      }
    });
  }

  toggleSelectAllPermissions() {
    const roleId = this.userForm.get('role_id')?.value;
    if (!roleId) return;

    if (this.areAllPermissionsSelected()) {
      this.userForm.patchValue({ permission_ids: [] }, { emitEvent: false });
    } else {
      // Get all visible permission IDs
      const visiblePermIds: number[] = [];
      this.permissionGroups.forEach(group => {
        group.permissions.forEach(perm => {
          if (this.isPermissionVisible(group.label, perm.name)) {
            const id = this.getPermissionId(perm.name);
            if (id) visiblePermIds.push(id);
          }
        });
      });
      this.userForm.patchValue({ permission_ids: visiblePermIds }, { emitEvent: false });
    }
  }

  areAllPermissionsSelected(): boolean {
    const roleId = this.userForm.get('role_id')?.value;
    if (!roleId) return false;

    const currentIds = this.userForm.get('permission_ids')?.value as number[] || [];
    
    let visibleCount = 0;
    let selectedVisibleCount = 0;

    this.permissionGroups.forEach(group => {
      group.permissions.forEach(perm => {
        if (this.isPermissionVisible(group.label, perm.name)) {
          visibleCount++;
          const id = this.getPermissionId(perm.name);
          if (currentIds.includes(id)) {
            selectedVisibleCount++;
          }
        }
      });
    });

    return visibleCount > 0 && visibleCount === selectedVisibleCount;
  }

  private getModal(id: string) {
    const modalElement = document.getElementById(id);
    if (modalElement) {
      return (window as any).bootstrap.Modal.getOrCreateInstance(modalElement);
    }
    return null;
  }

  hasVisiblePermissions(group: { label: string, permissions: { name: string, label: string }[] }): boolean {
    return group.permissions.some(p => this.isPermissionVisible(group.label, p.name));
  }

  loadOffices() {
    this.userService.listOffices().subscribe({
      next: (offices) => {
        this.officeOptions = offices;
      },
      error: () => {
        console.error('Failed to load offices');
      }
    });
  }

  loadRoles() {
    this.userService.listRoles().subscribe({
      next: (roles) => {
        this.roleOptions = roles;
      },
      error: () => {
        console.error('Failed to load roles');
      }
    });
  }

  loadPermissions() {
    this.userService.listPermissions().subscribe({
      next: (perms) => {
        this.allPermissions = perms;
      },
      error: () => {
        console.error('Failed to load permissions');
      }
    });
  }

  private loadingPermissionsForRoleId: number | null = null;

  updatePermissionsByRole(roleId: number) {
    const numericRoleId = Number(roleId);
    if (!numericRoleId) return;

    this.loadingPermissionsForRoleId = numericRoleId;
    this.userService.getRolePermissions(numericRoleId).subscribe({
      next: (perms) => {
        // Only apply if this is still the selected role
        const currentRoleIdValue = this.userForm.get('role_id')?.value;
        const currentRoleId = currentRoleIdValue ? Number(currentRoleIdValue) : null;
        
        if (this.loadingPermissionsForRoleId === numericRoleId && currentRoleId === numericRoleId) {
          const permIds = perms.map(p => Number(p.id));
          this.userForm.patchValue({ permission_ids: permIds }, { emitEvent: false });
        }
        this.loadingPermissionsForRoleId = null;
      },
      error: (err) => {
        console.error('Error fetching role permissions:', err);
        this.loadingPermissionsForRoleId = null;
      }
    });
  }

  isPermissionVisible(groupLabel: string, permissionName: string): boolean {
    const roleIdValue = this.userForm.get('role_id')?.value;
    if (!roleIdValue) return false;

    const roleId = Number(roleIdValue);
    const role = this.roleOptions.find(r => Number(r.id) === roleId);
    if (!role) return false;

    const roleName = role.role_name.toLowerCase();

    if (roleName === 'admin' || roleName === 'superadmin') {
      return true;
    }

    if (roleName === 'user') {
      // Only show first 4 of Requests group
      if (groupLabel !== 'Requests') return false;
      const userAllowedRequests = [
        'view_pending_requests',
        'view_approved_requests',
        'view_released_requests',
        'view_disapproved_requests'
      ];
      return userAllowedRequests.includes(permissionName);
    }

    return false;
  }

  filterPermissionsByRoleVisibility(roleId: number) {
    const role = this.roleOptions.find(r => Number(r.id) === roleId);
    if (!role) return;

    const roleName = role.role_name.toLowerCase();
    const currentIds = (this.userForm.get('permission_ids')?.value as any[] || []).map(id => Number(id));
    
    // Filter out permission IDs that are not visible/valid for this new role
    const filteredIds = currentIds.filter(id => {
      const permission = this.allPermissions.find(p => Number(p.id) === id);
      if (!permission) return false;
      
      const group = this.permissionGroups.find(g => 
        g.permissions.some(p => p.name === permission.name)
      );
      if (!group) return false;
      
      return this.isPermissionVisible(group.label, permission.name);
    });

    this.userForm.patchValue({ permission_ids: filteredIds }, { emitEvent: false });
  }

  onPermissionChange(event: any, permissionId: number) {
    const currentIds = (this.userForm.get('permission_ids')?.value as any[] || []).map(id => Number(id));
    const targetId = Number(permissionId);
    let newIds: number[];
    
    if (event.target.checked) {
      if (!currentIds.includes(targetId)) {
        newIds = [...currentIds, targetId];
      } else {
        newIds = currentIds;
      }
    } else {
      newIds = currentIds.filter(id => id !== targetId);
    }
    
    this.userForm.patchValue({ permission_ids: newIds }, { emitEvent: false });
  }

  isPermissionChecked(permissionName: string): boolean {
    const permission = this.allPermissions.find(p => p.name === permissionName);
    if (!permission) return false;
    const permissionIds = this.userForm.get('permission_ids')?.value as any[] || [];
    const targetId = Number(permission.id);
    return permissionIds.some(id => Number(id) === targetId);
  }

  getPermissionId(name: string): number {
    return this.allPermissions.find(p => p.name === name)?.id || 0;
  }

  loadUsers(page = 1) {
    this.isLoading.set(true);

    this.userService.listUsers(page, this.searchControl.value || '').subscribe({
      next: (result) => {
        this.users.set(result.data || []);
        this.currentPage.set(result.current_page);
        this.lastPage.set(result.last_page);
        this.totalUsers.set(result.total);
        this.isLoading.set(false);
      },
      error: () => {
        this.toastService.error('Unable to load users. Please check your network or login status.');
        this.isLoading.set(false);
      },
    });
  }

  openAddUser() {
    this.editMode.set(false);
    this.activeUser.set(null);

    this.userForm.reset({
      first_name: '',
      middle_initial: '',
      last_name: '',
      email: '',
      role_id: null,
      office_name: '',
      permission_ids: [],
      override_permissions: false,
    }, { emitEvent: false });

    this.userForm.get('role_id')?.enable();
    this.getModal('userModal')?.show();
  }

  editUser(user: User) {
    this.editMode.set(true);
    this.activeUser.set(user);

    // Use direct user permissions if they exist, otherwise fall back to role defaults
    const userPermIds = user.permissions?.map(p => Number(p.id)) || [];
    const rolePermIds = user.role?.permissions?.map(p => Number(p.id)) || [];
    
    // If customized, use user permissions (even if empty). 
    // If not customized, use role defaults for the initial view.
    const initialPermIds = user.has_custom_permissions ? userPermIds : rolePermIds;

    // Use emitEvent: false to prevent the role_id change from triggering updatePermissionsByRole
    // which would overwrite our merged permissions with role defaults.
    this.userForm.patchValue({
      first_name: user.first_name,
      middle_initial: user.middle_initial ?? '',
      last_name: user.last_name,
      email: user.email,
      role_id: user.role_id,
      office_name: user.office?.office_name || '',
      permission_ids: initialPermIds,
      override_permissions: !!user.has_custom_permissions,
    }, { emitEvent: false });

    const roleName = user.role?.role_name?.toLowerCase();
    if (roleName === 'superadmin') {
      this.userForm.get('role_id')?.disable();
    } else {
      this.userForm.get('role_id')?.enable();
    }

    this.getModal('userModal')?.show();
  }

  saveUser() {
    if (this.userForm.invalid) {
      this.toastService.warning('Please fill in all required fields before saving.');
      return;
    }

    const formValue = this.userForm.getRawValue();
    const payload: any = {
      first_name: formValue.first_name,
      middle_initial: formValue.middle_initial,
      last_name: formValue.last_name,
      email: formValue.email,
      role_id: formValue.role_id,
      office_name: formValue.office_name,
    };

    // If override is enabled, send the permission list (even if empty).
    // If disabled, send null to tell the backend to use Role defaults.
    payload.permission_ids = formValue.override_permissions ? formValue.permission_ids : null;

    if (this.editMode() && this.activeUser()) {
      this.userService.updateUser(this.activeUser()!.id, payload).subscribe({
        next: () => {
          this.toastService.success('User updated successfully.');
          this.getModal('userModal')?.hide();
          this.loadUsers(this.currentPage());
        },
        error: () => {
          this.toastService.error('Failed to update user. Make sure role and office IDs are valid.');
        },
      });
    } else {
      this.userService.createUser(payload).subscribe({
        next: () => {
          this.toastService.success('New user created successfully. A password reset email has been sent to the user.');
          this.getModal('userModal')?.hide();
          this.loadUsers(1);
        },
        error: () => {
          this.toastService.error('Failed to create user. Check required fields and permission.');
        },
      });
    }
  }

  cancelForm() {
    this.getModal('userModal')?.hide();
    this.activeUser.set(null);
  }

  async confirmDelete(user: User) {
    const confirmed = await this.confirmService.confirm(`Are you sure you want to delete user "${user.first_name} ${user.last_name}"?`, {
      title: 'Delete User',
      confirmText: 'Delete',
      danger: true
    });

    if (confirmed) {
      this.userService.deleteUser(user.id).subscribe({
        next: () => {
          this.toastService.success('User deleted successfully.');
          this.loadUsers(this.currentPage());
        },
        error: () => {
          this.toastService.error('Failed to delete user. You may not have permission.');
        },
      });
    }
  }

  pageNumbers = computed(() => {
    const current = this.currentPage();
    const last = this.lastPage();
    const delta = 2;

    let start = Math.max(1, current - delta);
    let end = Math.min(last, current + delta);

    if (end - start + 1 < 5) {
      if (start === 1) {
        end = Math.min(last, 5);
      } else if (end === last) {
        start = Math.max(1, last - 4);
      }
    }

    const pages = [];
    for (let i = start; i <= end; i++) {
      pages.push(i);
    }
    return pages;
  });

  setPage(page: number) {
    if (page >= 1 && page <= this.lastPage()) {
      this.loadUsers(page);
    }
  }

  previousPage() {
    if (this.currentPage() > 1) {
      this.setPage(this.currentPage() - 1);
    }
  }

  nextPage() {
    if (this.currentPage() < this.lastPage()) {
      this.setPage(this.currentPage() + 1);
    }
  }

  trackByUser(_index: number, user: User) {
    return user.id;
  }

  openBackupModal() {
    this.getModal('backupModal')?.show();
  }

  downloadBackup() {
    this.isProcessingBackup.set(true);
    this.userService.backupDatabase().subscribe({
      next: (blob) => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `backup-${new Date().toISOString().slice(0, 19).replace(/:/g, '-')}.sql`;
        a.click();
        window.URL.revokeObjectURL(url);
        this.isProcessingBackup.set(false);
        this.toastService.success('Backup downloaded successfully.');
        this.getModal('backupModal')?.hide();
      },
      error: () => {
        this.isProcessingBackup.set(false);
        this.toastService.error('Failed to generate backup.');
      }
    });
  }

  openRestoreModal() {
    this.restoreFile = null;
    this.restorePassword.set('');
    this.showRestorePassword.set(false);
    this.getModal('restoreModal')?.show();
  }

  onFileSelected(event: any) {
    const file = event.target.files[0];
    if (file) {
      this.restoreFile = file;
    }
  }

  performRestore() {
    if (!this.restoreFile) {
      this.toastService.warning('Please select a SQL file to restore.');
      return;
    }

    if (!this.restorePassword()) {
      this.toastService.warning('Please enter your password to confirm restoration.');
      return;
    }

    this.isProcessingRestore.set(true);
    this.userService.restoreDatabase(this.restoreFile, this.restorePassword()).subscribe({
      next: () => {
        this.isProcessingRestore.set(false);
        this.toastService.success('Database restored successfully.');
        this.getModal('restoreModal')?.hide();
        this.loadUsers(1);
      },
      error: (err) => {
        this.isProcessingRestore.set(false);
        this.toastService.error(err.error?.message || 'Failed to restore database.');
      }
    });
  }
}

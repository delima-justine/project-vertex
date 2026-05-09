import { Component, inject, signal, effect } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormArray, FormBuilder, FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Sidebar } from '../sidebar/sidebar';
import { User, Office, Role, Permission } from '../../models/smis.model';
import { UserManagementService } from '../../services/user-management.service';
import { TopNav } from "../top-nav/top-nav";
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-user-management',
  imports: [CommonModule, ReactiveFormsModule, Sidebar, TopNav],
  templateUrl: './user-management.html',
  styleUrl: './user-management.scss',
})
export class UserManagement {
  userService = inject(UserManagementService);
  formBuilder = inject(FormBuilder);
  authService = inject(AuthService);

  users = signal<User[]>([]);
  isLoading = signal(false);
  currentPage = signal(1);
  lastPage = signal(1);
  totalUsers = signal(0);
  editMode = signal(false);
  activeUser = signal<User | null>(null);
  userToDelete = signal<User | null>(null);
  feedback = signal('');

  searchControl = new FormControl('');

  userForm: FormGroup = this.formBuilder.group({
    first_name: ['', Validators.required],
    middle_initial: ['', Validators.maxLength(1)],
    last_name: ['', Validators.required],
    email: ['', [Validators.required, Validators.email]],
    role_id: [null, Validators.required],
    office_name: ['', Validators.required],
    permission_ids: [[]],
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

    // Reset permissions when role changes
    this.userForm.get('role_id')?.valueChanges.subscribe(roleId => {
      if (roleId) {
        this.updatePermissionsByRole(roleId);
      } else {
        this.userForm.patchValue({ permission_ids: [] });
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

  updatePermissionsByRole(roleId: number) {
    this.userService.getRolePermissions(roleId).subscribe({
      next: (perms) => {
        const permIds = perms.map(p => p.id);
        // Only patch if role_id currently matches the one we fetched for
        // and we aren't in a state that should ignore defaults
        if (this.userForm.get('role_id')?.value == roleId) {
          setTimeout(() => {
            this.userForm.patchValue({ permission_ids: permIds }, { emitEvent: false });
          });
        }
      }
    });
  }

  isPermissionVisible(groupLabel: string, permissionName: string): boolean {
    const roleId = this.userForm.get('role_id')?.value;
    if (!roleId) return false;

    const role = this.roleOptions.find(r => r.id == roleId);
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

  onPermissionChange(event: any, permissionId: number) {
    const permissionIds = this.userForm.get('permission_ids')?.value as number[];
    if (event.target.checked) {
      if (!permissionIds.includes(permissionId)) {
        this.userForm.patchValue({ permission_ids: [...permissionIds, permissionId] }, { emitEvent: false });
      }
    } else {
      this.userForm.patchValue({ 
        permission_ids: permissionIds.filter(id => id !== permissionId) 
      }, { emitEvent: false });
    }
  }

  isPermissionChecked(permissionName: string): boolean {
    const permission = this.allPermissions.find(p => p.name === permissionName);
    if (!permission) return false;
    const permissionIds = this.userForm.get('permission_ids')?.value as number[] || [];
    return permissionIds.includes(permission.id);
  }

  getPermissionId(name: string): number {
    return this.allPermissions.find(p => p.name === name)?.id || 0;
  }

  loadUsers(page = 1) {
    this.isLoading.set(true);
    this.feedback.set('');

    this.userService.listUsers(page, this.searchControl.value || '').subscribe({
      next: (result) => {
        this.users.set(result.data || []);
        this.currentPage.set(result.current_page);
        this.lastPage.set(result.last_page);
        this.totalUsers.set(result.total);
        this.isLoading.set(false);
      },
      error: () => {
        this.feedback.set('Unable to load users. Please check your network or login status.');
        this.isLoading.set(false);
      },
    });
  }

  openAddUser() {
    this.editMode.set(false);
    this.activeUser.set(null);
    this.feedback.set('');

    this.userForm.reset({
      first_name: '',
      middle_initial: '',
      last_name: '',
      email: '',
      role_id: null,
      office_name: '',
      permission_ids: [],
    }, { emitEvent: false });

    this.userForm.get('role_id')?.enable();
    this.getModal('userModal')?.show();
  }

  editUser(user: User) {
    this.editMode.set(true);
    this.activeUser.set(user);
    this.feedback.set('');

    // Use direct user permissions if they exist, otherwise fall back to role defaults
    const userPermIds = user.permissions?.map(p => p.id) || [];
    const rolePermIds = user.role?.permissions?.map(p => p.id) || [];
    const initialPermIds = userPermIds.length > 0 ? userPermIds : rolePermIds;

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
    }, { emitEvent: false });

    if (user.role?.role_name?.toLowerCase() === 'superadmin') {
      this.userForm.get('role_id')?.disable();
    } else {
      this.userForm.get('role_id')?.enable();
    }

    this.getModal('userModal')?.show();
  }

  saveUser() {
    if (this.userForm.invalid) {
      this.feedback.set('Please fill in all required fields before saving.');
      return;
    }

    const payload = { ...this.userForm.value };

    if (this.editMode() && this.activeUser()) {
      this.userService.updateUser(this.activeUser()!.id, payload).subscribe({
        next: () => {
          this.feedback.set('User updated successfully.');
          this.getModal('userModal')?.hide();
          this.loadUsers(this.currentPage());
        },
        error: () => {
          this.feedback.set('Failed to update user. Make sure role and office IDs are valid.');
        },
      });
    } else {
      this.userService.createUser(payload).subscribe({
        next: () => {
          this.feedback.set('New user created successfully. A password reset email has been sent to the user.');
          this.getModal('userModal')?.hide();
          this.loadUsers(1);
        },
        error: () => {
          this.feedback.set('Failed to create user. Check required fields and permission.');
        },
      });
    }
  }

  cancelForm() {
    this.getModal('userModal')?.hide();
    this.activeUser.set(null);
    this.feedback.set('');
  }

  confirmDelete(user: User) {
    this.userToDelete.set(user);
    this.getModal('deleteConfirmModal')?.show();
  }

  cancelDelete() {
    this.getModal('deleteConfirmModal')?.hide();
    this.userToDelete.set(null);
  }

  deleteUser() {
    const user = this.userToDelete();
    if (!user) return;

    this.userService.deleteUser(user.id).subscribe({
      next: () => {
        this.feedback.set('User deleted successfully.');
        this.getModal('deleteConfirmModal')?.hide();
        this.userToDelete.set(null);
        this.loadUsers(this.currentPage());
      },
      error: () => {
        this.feedback.set('Failed to delete user. You may not have permission.');
        this.getModal('deleteConfirmModal')?.hide();
      },
    });
  }

  previousPage() {
    if (this.currentPage() > 1) {
      this.loadUsers(this.currentPage() - 1);
    }
  }

  nextPage() {
    if (this.currentPage() < this.lastPage()) {
      this.loadUsers(this.currentPage() + 1);
    }
  }

  trackByUser(_index: number, user: User) {
    return user.id;
  }
}

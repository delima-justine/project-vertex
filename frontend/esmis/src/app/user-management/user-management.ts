import { Component, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Sidebar } from '../sidebar/sidebar';
import { User, Office, Role } from '../../models/smis.model';
import { UserManagementService } from '../../services/user-management.service';
import { TopNav } from "../top-nav/top-nav";

@Component({
  selector: 'app-user-management',
  imports: [CommonModule, ReactiveFormsModule, Sidebar, TopNav],
  templateUrl: './user-management.html',
  styleUrl: './user-management.scss',
})
export class UserManagement {
  userService = inject(UserManagementService);
  formBuilder = inject(FormBuilder);

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
    office_id: [null, Validators.required],
  });

  roleOptions: Role[] = [];
  officeOptions: Office[] = [];

  constructor() {
    this.loadUsers();
    this.loadOffices();
    this.loadRoles();
  }

  private getModal(id: string) {
    const modalElement = document.getElementById(id);
    if (modalElement) {
      return (window as any).bootstrap.Modal.getOrCreateInstance(modalElement);
    }
    return null;
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
      office_id: null,
    });

    this.getModal('userModal')?.show();
  }

  editUser(user: User) {
    this.editMode.set(true);
    this.activeUser.set(user);
    this.feedback.set('');

    this.userForm.patchValue({
      first_name: user.first_name,
      middle_initial: user.middle_initial ?? '',
      last_name: user.last_name,
      email: user.email,
      role_id: user.role_id,
      office_id: user.office_id,
    });

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

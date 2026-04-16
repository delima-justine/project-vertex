import { Component, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Sidebar } from '../sidebar/sidebar';
import { User, Office } from '../../models/smis.model';
import { UserManagementService } from '../../services/user-management.service';

@Component({
  selector: 'app-user-management',
  imports: [CommonModule, ReactiveFormsModule, Sidebar],
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
  formVisible = signal(false);
  editMode = signal(false);
  activeUser = signal<User | null>(null);
  feedback = signal('');

  searchControl = new FormControl('');

  userForm: FormGroup = this.formBuilder.group({
    first_name: ['', Validators.required],
    middle_initial: ['', Validators.maxLength(1)],
    last_name: ['', Validators.required],
    email: ['', [Validators.required, Validators.email]],
    role_id: [null, Validators.required],
    office_id: [null, Validators.required],
    password: ['', [Validators.minLength(8)]],
  });

  roleOptions = [
    { id: 1, name: 'admin' },
    { id: 2, name: 'superadmin' },
    { id: 3, name: 'user' },
  ];

  officeOptions: Office[] = [];

  constructor() {
    this.loadUsers();
    this.loadOffices();
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
    this.formVisible.set(true);
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
      password: '',
    });

    this.userForm.get('password')?.setValidators([Validators.required, Validators.minLength(8)]);
    this.userForm.get('password')?.updateValueAndValidity();
  }

  editUser(user: User) {
    this.formVisible.set(true);
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
      password: '',
    });

    this.userForm.get('password')?.setValidators([Validators.minLength(8)]);
    this.userForm.get('password')?.updateValueAndValidity();
  }

  saveUser() {
    if (this.userForm.invalid) {
      this.feedback.set('Please fill in all required fields before saving.');
      return;
    }

    const payload = { ...this.userForm.value } as any;
    if (!payload.password) {
      delete payload.password;
    }

    if (this.editMode() && this.activeUser()) {
      this.userService.updateUser(this.activeUser()!.id, payload).subscribe({
        next: () => {
          this.feedback.set('User updated successfully.');
          this.formVisible.set(false);
          this.loadUsers(this.currentPage());
        },
        error: () => {
          this.feedback.set('Failed to update user. Make sure role and office IDs are valid.');
        },
      });
    } else {
      this.userService.createUser(payload).subscribe({
        next: () => {
          this.feedback.set('New user created successfully.');
          this.formVisible.set(false);
          this.loadUsers(1);
        },
        error: () => {
          this.feedback.set('Failed to create user. Check required fields and permission.');
        },
      });
    }
  }

  cancelForm() {
    this.formVisible.set(false);
    this.activeUser.set(null);
    this.feedback.set('');
  }

  deleteUser(user: User) {
    if (!confirm(`Delete ${user.first_name} ${user.last_name}?`)) {
      return;
    }

    this.userService.deleteUser(user.id).subscribe({
      next: () => {
        this.feedback.set('User deleted successfully.');
        this.loadUsers(this.currentPage());
      },
      error: () => {
        this.feedback.set('Failed to delete user. You may not have permission.');
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

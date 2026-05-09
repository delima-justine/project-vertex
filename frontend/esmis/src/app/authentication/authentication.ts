import { Component, inject, signal, computed } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-authentication',
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './authentication.html',
  styleUrl: './authentication.scss',
})
export class Authentication {
  showPassword = signal(false);
  router = inject(Router);
  loginForm: FormGroup;
  formBuilder = inject(FormBuilder);
  authService = inject(AuthService);
  isFailed = signal(false);
  notifMessage = signal('');
  notifType = signal<'success' | 'error' | 'warning'>('success');
  notifTitle = signal('');
  notifIcon = computed(() => this.notifType() === 'success' ? 'check-circle' : this.notifType() === 'error' ? 'x-circle' : 'exclamation-triangle');

  constructor() {
    this.loginForm = this.formBuilder.group({
      email: ['', [Validators.required]],
      password: ['', Validators.required],
    });
  }

  togglePasswordVisibility() {
    this.showPassword.set(!this.showPassword());
  }

  showNotification(message: string, type: 'success' | 'error' | 'warning') {
    this.notifMessage.set(message);
    this.notifType.set(type);
    this.notifTitle.set(type === 'success' ? 'Success' : type === 'error' ? 'Error' : 'Warning');
    const modalElement = document.getElementById('notifModal-auth');
    if (modalElement) {
      const modal = (window as any).bootstrap.Modal.getOrCreateInstance(modalElement);
      modal.show();
    }
  }

  login() {
    const loginValue = this.loginForm.value;

    if(this.loginForm.valid) {
      this.authService.login(loginValue).subscribe({
        next: (res) => {
          console.log('Login successful:', res);
          console.log('User permissions:', res.permissions);
          this.isFailed.set(false);
          this.router.navigate(['/home']);
        },
        error: (err) => {
          console.error('Login failed:', err);
          this.showNotification('Invalid email or password. Please try again.', 'error');
          this.loginForm.reset();
          this.isFailed.set(true);
        }
      });
    }
  }

  navigateToForgotPassword() {
    this.router.navigate(['/forgot-pass']);
  }
}

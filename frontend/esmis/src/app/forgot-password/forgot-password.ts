import { Component, inject, signal } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { AuthService } from '../../services/auth.service';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';

@Component({
  selector: 'app-forgot-password',
  imports: [ReactiveFormsModule, CommonModule],
  templateUrl: './forgot-password.html',
  styleUrl: './forgot-password.scss',
})
export class ForgotPassword {
  forgotPasswordForm: FormGroup;
  formBuilder = inject(FormBuilder);
  authService = inject(AuthService);
  
  message = signal<string | null>(null);
  error = signal<string | null>(null);
  isLoading = signal<boolean>(false);

  router = inject(Router);

  constructor() {
    this.forgotPasswordForm = this.formBuilder.group({
      email: ['', [Validators.required, Validators.email]],
    });
  }

  sendResetLink() {
    if (this.forgotPasswordForm.invalid) return;

    this.isLoading.set(true);
    this.message.set(null);
    this.error.set(null);

    this.authService.forgotPassword(this.forgotPasswordForm.value).subscribe({
      next: (response) => {
        this.message.set(response.message);
        this.isLoading.set(false);
      },
      error: (err) => {
        this.error.set(err.error?.message || 'Something went wrong. Please try again.');
        this.isLoading.set(false);
      }
    });
  }

  navigateToLogin() {
    this.router.navigate(['/']);
  }

  get email() {
    return this.forgotPasswordForm.get('email');
  }
}

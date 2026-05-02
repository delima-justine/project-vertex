import { Component, inject, OnInit, signal } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-reset-password',
  imports: [ReactiveFormsModule, CommonModule],
  templateUrl: './reset-password.html',
  styleUrl: './reset-password.scss',
})
export class ResetPassword implements OnInit {
  resetPasswordForm: FormGroup;
  formBuilder = inject(FormBuilder);
  route = inject(ActivatedRoute);
  router = inject(Router);
  authService = inject(AuthService);

  message = signal<string | null>(null);
  error = signal<string | null>(null);
  isLoading = signal<boolean>(false);

  token: string | null = null;
  email: string | null = null;

  constructor() {
    this.resetPasswordForm = this.formBuilder.group({
      password: ['', [Validators.required, Validators.minLength(8)]],
      password_confirmation: ['', [Validators.required]],
    }, {
      validators: this.passwordMatchValidator
    });
  }

  ngOnInit(): void {
    this.token = this.route.snapshot.queryParamMap.get('token');
    this.email = this.route.snapshot.queryParamMap.get('email');

    if (!this.token || !this.email) {
      this.error.set('Invalid or expired reset link.');
    }
  }

  passwordMatchValidator(g: FormGroup) {
    return g.get('password')?.value === g.get('password_confirmation')?.value
      ? null : { 'mismatch': true };
  }

  resetPassword() {
    if (this.resetPasswordForm.invalid || !this.token || !this.email) return;

    if (confirm('Are you sure you want to reset your password?')) {
      this.isLoading.set(true);
      this.message.set(null);
      this.error.set(null);

      const payload = {
        ...this.resetPasswordForm.value,
        token: this.token,
        email: this.email
      };

      this.authService.resetPassword(payload).subscribe({
        next: (response) => {
          this.message.set(response.message);
          this.isLoading.set(false);
          setTimeout(() => this.router.navigate(['/']), 3000);
        },
        error: (err) => {
          this.error.set(err.error?.message || 'Something went wrong. Please try again.');
          this.isLoading.set(false);
        }
      });
    }
  }

  get password() {
    return this.resetPasswordForm.get('password');
  }

  get password_confirmation() {
    return this.resetPasswordForm.get('password_confirmation');
  }
}

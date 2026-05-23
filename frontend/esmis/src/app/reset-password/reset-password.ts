import { Component, inject, OnDestroy, OnInit, signal } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { ConfirmService } from '../../services/confirm.service';
import { CommonModule } from '@angular/common';
import { interval, Subscription } from 'rxjs';
import { strongPasswordValidator } from '../../validators/password.validator';
import { environment } from '../../environments/environment';

@Component({
  selector: 'app-reset-password',
  imports: [ReactiveFormsModule, CommonModule],
  templateUrl: './reset-password.html',
  styleUrl: './reset-password.scss',
})
export class ResetPassword implements OnInit, OnDestroy {
  public env = environment;
  resetPasswordForm: FormGroup;
  formBuilder = inject(FormBuilder);
  route = inject(ActivatedRoute);
  router = inject(Router);
  authService = inject(AuthService);
  confirmService = inject(ConfirmService);

  message = signal<string | null>(null);
  error = signal<string | null>(null);
  isLoading = signal<boolean>(false);
  timeRemaining = signal<string | null>(null);
  isExpired = signal<boolean>(false);
  showPassword = signal<boolean>(false);
  showConfirmPassword = signal<boolean>(false);

  token: string | null = null;
  email: string | null = null;
  timerSubscription: Subscription | null = null;

  constructor() {
    this.resetPasswordForm = this.formBuilder.group({
      password: ['', [Validators.required, Validators.minLength(8), strongPasswordValidator()]],
      password_confirmation: ['', [Validators.required]],
    }, {
      validators: this.passwordMatchValidator
    });
  }

  ngOnInit(): void {
    this.token = this.route.snapshot.queryParamMap.get('token');
    this.email = this.route.snapshot.queryParamMap.get('email');

    if (!this.token || !this.email) {
      this.router.navigate(['/']);
      return;
    }

    this.checkToken();
  }

  ngOnDestroy(): void {
    this.timerSubscription?.unsubscribe();
  }

  checkToken() {
    this.isLoading.set(true);
    this.authService.checkResetToken({ email: this.email!, token: this.token! }).subscribe({
      next: (response) => {
        this.isLoading.set(false);
        this.startTimer(response.expires_at);
      },
      error: (err) => {
        this.isLoading.set(false);
        this.error.set(err.error?.message || 'Invalid or expired reset link.');
        this.isExpired.set(true);
      }
    });
  }

  startTimer(expiresAt: string) {
    const expiryDate = new Date(expiresAt).getTime();
    
    // Initial check
    this.updateTimer(expiryDate);

    this.timerSubscription = interval(1000).subscribe(() => {
      this.updateTimer(expiryDate);
    });
  }

  updateTimer(expiryDate: number) {
    const now = new Date().getTime();
    const distance = expiryDate - now;

    if (distance <= 0) {
      this.timeRemaining.set('00:00');
      this.isExpired.set(true);
      this.error.set('This reset link has expired.');
      this.timerSubscription?.unsubscribe();
      return;
    }

    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    this.timeRemaining.set(
      `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
    );
  }

  togglePasswordVisibility(field: 'password' | 'confirm') {
    if (field === 'password') {
      this.showPassword.update(v => !v);
    } else {
      this.showConfirmPassword.update(v => !v);
    }
  }

  passwordMatchValidator(g: FormGroup) {
    return g.get('password')?.value === g.get('password_confirmation')?.value
      ? null : { 'mismatch': true };
  }

  async resetPassword() {
    if (this.resetPasswordForm.invalid || !this.token || !this.email) return;

    const confirmed = await this.confirmService.confirm('Are you sure you want to reset your password?', {
      title: 'Reset Password',
      confirmText: 'Reset'
    });

    if (confirmed) {
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

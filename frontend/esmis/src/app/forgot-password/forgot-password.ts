import { Component, inject, OnInit, signal } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { AuthService } from '../../services/auth.service';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router } from '@angular/router';
import { environment } from '../../environments/environment';

@Component({
  selector: 'app-forgot-password',
  imports: [ReactiveFormsModule, CommonModule],
  templateUrl: './forgot-password.html',
  styleUrl: './forgot-password.scss',
})
export class ForgotPassword implements OnInit {
  public env = environment;
  forgotPasswordForm: FormGroup;
  formBuilder = inject(FormBuilder);
  authService = inject(AuthService);
  route = inject(ActivatedRoute);
  
  message = signal<string | null>(null);
  error = signal<string | null>(null);
  isLoading = signal<boolean>(false);

  router = inject(Router);

  constructor() {
    this.forgotPasswordForm = this.formBuilder.group({
      email: ['', [Validators.required, Validators.email]],
    });
  }

  ngOnInit(): void {
    const status = this.route.snapshot.queryParamMap.get('status');
    const autoResend = this.route.snapshot.queryParamMap.get('auto_resend');
    const emailParam = this.route.snapshot.queryParamMap.get('email');

    if (status === 'resent') {
      this.message.set('A new reset link has been sent to your email.');
    }

    if (autoResend === 'true' && emailParam) {
      this.forgotPasswordForm.patchValue({ email: emailParam });
      this.sendResetLink();
    }
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

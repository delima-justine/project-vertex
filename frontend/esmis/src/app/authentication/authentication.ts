import { Component, inject, signal } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { ToastService } from '../../services/toast.service';

@Component({
  selector: 'app-authentication',
  imports: [ReactiveFormsModule],
  templateUrl: './authentication.html',
  styleUrl: './authentication.scss',
})
export class Authentication {
  showPassword = signal(false);
  router = inject(Router);
  loginForm: FormGroup;
  formBuilder = inject(FormBuilder);
  authService = inject(AuthService);
  toastService = inject(ToastService);
  isFailed = signal(false);

  constructor() {
    this.loginForm = this.formBuilder.group({
      email: ['', [Validators.required]],
      password: ['', Validators.required],
    });
  }

  togglePasswordVisibility() {
    this.showPassword.set(!this.showPassword());
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
          this.toastService.error("Invalid email or password. Please try again.");
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

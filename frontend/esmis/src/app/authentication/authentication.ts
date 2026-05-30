import { Component, inject, signal } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { ToastService } from '../../services/toast.service';
import { environment } from '../../environments/environment';

@Component({
  selector: 'app-authentication',
  imports: [ReactiveFormsModule],
  templateUrl: './authentication.html',
  styleUrl: './authentication.scss',
})
export class Authentication {
  public env = environment;
  showPassword = signal(false);
  loginType = signal<'user' | 'admin'>('user');
  router = inject(Router);
  loginForm: FormGroup;
  formBuilder = inject(FormBuilder);
  authService = inject(AuthService);
  toastService = inject(ToastService);
  isFailed = signal(false);
  errorMessage = signal('Invalid email or password. Please try again.');

  constructor() {
    this.loginForm = this.formBuilder.group({
      email: ['', [Validators.required]],
      password: ['', Validators.required],
    });
  }

  togglePasswordVisibility() {
    this.showPassword.set(!this.showPassword());
  }

  setLoginType(type: 'user' | 'admin') {
    this.loginType.set(type);
    this.isFailed.set(false);
  }

  login() {
    if(this.loginForm.valid) {
      const payload = {
        ...this.loginForm.value,
        login_type: this.loginType()
      };

      this.authService.login(payload).subscribe({
        next: (res) => {
          this.isFailed.set(false);
          this.router.navigate(['/home']);
        },
        error: (err) => {
          console.error('Login failed:', err);
          
          let msg = 'Invalid email or password. Please try again.';
          if (err.error) {
            if (typeof err.error === 'string') {
              msg = err.error;
            } else if (err.error.message) {
              msg = err.error.message;
            }
          }
          
          this.errorMessage.set(msg);
          this.loginForm.get('password')?.reset();
          this.isFailed.set(true);
        }
      });
    }
  }

  navigateToForgotPassword() {
    this.router.navigate(['/forgot-pass']);
  }
}

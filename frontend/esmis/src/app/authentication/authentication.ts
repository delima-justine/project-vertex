import { Component, inject, signal } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-authentication',
  imports: [],
  templateUrl: './authentication.html',
  styleUrl: './authentication.scss',
})
export class Authentication {
  showPassword = signal(false);
  router = inject(Router);

  togglePasswordVisibility() {
    this.showPassword.set(!this.showPassword());
  }

  login() {
    // Initial Implementation
    this.router.navigate(['/home']);
  }
}

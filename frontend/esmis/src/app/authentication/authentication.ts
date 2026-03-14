import { Component, signal } from '@angular/core';

@Component({
  selector: 'app-authentication',
  imports: [],
  templateUrl: './authentication.html',
  styleUrl: './authentication.scss',
})
export class Authentication {
  showPassword = signal(false);

  togglePasswordVisibility() {
    this.showPassword.set(!this.showPassword());
  }
}

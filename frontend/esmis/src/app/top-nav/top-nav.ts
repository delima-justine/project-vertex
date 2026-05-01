import { Component, inject, signal } from '@angular/core';
import { AuthService } from '../../services/auth.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-top-nav',
  imports: [],
  templateUrl: './top-nav.html',
  styleUrl: './top-nav.scss',
})
export class TopNav {
  isOpen = signal(false);
  authService = inject(AuthService);
  user = this.authService.currentUser;
  router = inject(Router);

  
  toggleDropdown() {
    this.isOpen.set(!this.isOpen());
  }

  logout() {
    this.authService.logout().subscribe({
      next: () => {
        this.router.navigate(['/']);
      },
      error: (err) => {
        localStorage.removeItem('auth_token');
        this.authService.currentUser.set(null);
        this.router.navigate(['/']);
      }
    });
  }
}

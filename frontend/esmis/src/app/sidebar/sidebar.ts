import { Component, inject, signal } from '@angular/core';
import { RouterLink, RouterLinkActive } from "@angular/router";
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-sidebar',
  imports: [RouterLink, RouterLinkActive],
  templateUrl: './sidebar.html',
  styleUrl: './sidebar.scss',
})
export class Sidebar {
  authService = inject(AuthService);
  isDropdownOpen = signal(localStorage.getItem('isDropdownOpen') === 'true');

  toggleDropdown() {
    const next = !this.isDropdownOpen();
    this.isDropdownOpen.set(next);
    localStorage.setItem('isDropdownOpen', String(next));
  }
}

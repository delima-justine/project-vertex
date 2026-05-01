import { Component, inject, signal } from '@angular/core';
import { RouterLink, RouterLinkActive } from "@angular/router";
import { AuthService } from '../../services/auth.service';
import { LayoutService } from '../../services/layout.service';

@Component({
  selector: 'app-sidebar',
  standalone: true,
  imports: [RouterLink, RouterLinkActive],
  templateUrl: './sidebar.html',
  styleUrls: ['./sidebar.scss'],
})
export class Sidebar {
  authService: AuthService = inject(AuthService);
  layoutService: LayoutService = inject(LayoutService);
  isDropdownOpen = signal(localStorage.getItem('isDropdownOpen') === 'true');

  toggleDropdown() {
    const next = !this.isDropdownOpen();
    this.isDropdownOpen.set(next);
    localStorage.setItem('isDropdownOpen', String(next));
  }

  toggleSidebar() {
    this.layoutService.toggleSidebar();
  }
}

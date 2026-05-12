import { CommonModule } from '@angular/common';
import { Component, inject, OnInit, signal } from '@angular/core';
import { RouterLink, RouterLinkActive } from "@angular/router";
import { AuthService } from '../../services/auth.service';
import { LayoutService } from '../../services/layout.service';
import { ThemeService } from '../../services/theme';
import { NotificationApiService } from '../../services/notification-api.service';
import { NotificationService } from '../../services/notification.service';
import { SupplyService } from '../../services/supply.service';

@Component({
  selector: 'app-sidebar',
  standalone: true,
  imports: [RouterLink, RouterLinkActive, CommonModule],
  templateUrl: './sidebar.html',
  styleUrls: ['./sidebar.scss'],
})
export class Sidebar implements OnInit {
  public authService: AuthService = inject(AuthService);
  public layoutService: LayoutService = inject(LayoutService);
  public themeService: ThemeService = inject(ThemeService);
  public notifApiService = inject(NotificationApiService);
  public notifService = inject(NotificationService);
  public supplyService = inject(SupplyService);

  public isDropdownOpen = signal(localStorage.getItem('isDropdownOpen') === 'true');

  ngOnInit() {
    // Initial count fetch 
    if (this.authService.isLoggedIn()) {
      this.notifApiService.getUnreadCount().subscribe();
      this.supplyService.getStatusCounts().subscribe();
    }

    // Refresh counts when a notification arrives
    this.notifService.notifications$.subscribe(() => {
      this.supplyService.getStatusCounts().subscribe();
    });
  }

  toggleDropdown() {
    const next = !this.isDropdownOpen();
    this.isDropdownOpen.set(next);
    localStorage.setItem('isDropdownOpen', String(next));
  }

  toggleSidebar() {
    this.layoutService.toggleSidebar();
  }
}

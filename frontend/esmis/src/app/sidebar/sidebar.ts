import { Component, inject, OnInit, signal, AfterViewInit, ViewChild, ElementRef } from '@angular/core';
import { RouterLink, RouterLinkActive } from "@angular/router";
import { AuthService } from '../../services/auth.service';
import { LayoutService } from '../../services/layout.service';
import { ThemeService } from '../../services/theme';
import { NotificationApiService } from '../../services/notification-api.service';
import { NotificationService } from '../../services/notification.service';
import { SupplyService } from '../../services/supply.service';
import { environment } from '../../environments/environment';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-sidebar',
  standalone: true,
  imports: [RouterLink, RouterLinkActive, CommonModule],
  templateUrl: './sidebar.html',
  styleUrls: ['./sidebar.scss'],
})
export class Sidebar implements OnInit, AfterViewInit {
  @ViewChild('toggleBtn') toggleBtn!: ElementRef;

  public env = environment;
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

  ngAfterViewInit() {
    // Initialize Bootstrap tooltip for the toggle button
    const bootstrap = (window as any).bootstrap;
    if (bootstrap && this.toggleBtn) {
      new bootstrap.Tooltip(this.toggleBtn.nativeElement);
    }
  }

  toggleDropdown() {
    const next = !this.isDropdownOpen();
    this.isDropdownOpen.set(next);
    localStorage.setItem('isDropdownOpen', String(next));
  }

  toggleSidebar() {
    this.layoutService.toggleSidebar();
  }

  closeSidebarOnMobile() {
    if (window.innerWidth <= 1024) {
      this.layoutService.isSidebarOpen.set(false);
    }
  }
}

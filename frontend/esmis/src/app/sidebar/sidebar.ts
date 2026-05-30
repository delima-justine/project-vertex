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
  public tooltipText = signal(localStorage.getItem('sidebarTooltipShown') 
    ? 'Toggle Sidebar' : 'Click here to collapse or open the sidebar');

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
      const tooltip = new bootstrap.Tooltip(this.toggleBtn.nativeElement);

      // Show instructional tooltip for first-time desktop users
      if (window.innerWidth > 1024 && !localStorage.getItem('sidebarTooltipShown')) {
        setTimeout(() => {
          tooltip.show();
        }, 1000); // 1-second delay for visibility after load
      }
    }
  }

  toggleDropdown() {
    const next = !this.isDropdownOpen();
    this.isDropdownOpen.set(next);
    localStorage.setItem('isDropdownOpen', String(next));
  }

  toggleSidebar() {
    this.layoutService.toggleSidebar();

    // Mark as shown when the user interacts with the toggle button
    if (!localStorage.getItem('sidebarTooltipShown')) {
      localStorage.setItem('sidebarTooltipShown', '1');
      this.tooltipText.set('Toggle Sidebar');
      
      const bootstrap = (window as any).bootstrap;
      if (bootstrap && this.toggleBtn) {
        let tooltip = bootstrap.Tooltip.getInstance(this.toggleBtn.nativeElement);
        if (tooltip) {
          tooltip.hide();
          // Re-initialize to pick up the new data-bs-title from the signal update
          setTimeout(() => {
            tooltip?.dispose();
            new bootstrap.Tooltip(this.toggleBtn.nativeElement);
          }, 500);
        }
      }
    }
  }

  closeSidebarOnMobile() {
    if (window.innerWidth <= 1024) {
      this.layoutService.isSidebarOpen.set(false);
    }
  }
}

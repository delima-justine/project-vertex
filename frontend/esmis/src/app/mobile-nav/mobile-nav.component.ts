import { CommonModule } from '@angular/common';
import { Component, OnInit, inject, signal, HostListener } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { LayoutService } from '../../services/layout.service';
import { NotificationApiService } from '../../services/notification-api.service';
import { SupplyService } from '../../services/supply.service';

@Component({
  selector: 'app-mobile-nav',
  standalone: true,
  imports: [CommonModule, RouterLink, RouterLinkActive],
  templateUrl: './mobile-nav.component.html',
  styleUrls: ['./mobile-nav.component.scss'],
})
export class MobileNavComponent implements OnInit {
  public drawerOpen = signal(false);
  public isNavHidden = signal(false);
  private lastScrollY = 0;
  public authService = inject(AuthService);
  public layoutService = inject(LayoutService);
  public notifApiService = inject(NotificationApiService);
  public supplyService = inject(SupplyService);

  ngOnInit() {
    if (this.authService.isLoggedIn()) {
      this.notifApiService.getUnreadCount().subscribe();
      this.supplyService.getStatusCounts().subscribe();
    }
  }

  @HostListener('window:scroll', [])
  onWindowScroll() {
    const currentScrollY = window.scrollY;
    if (window.innerWidth <= 991) {
      if (currentScrollY > this.lastScrollY && currentScrollY > 100) {
        this.isNavHidden.set(true);
      } else if (currentScrollY < this.lastScrollY) {
        this.isNavHidden.set(false);
      }
    }
    this.lastScrollY = currentScrollY;
  }

  public isSidebarOpenOnMobile() {
    return window.innerWidth <= 991 && this.layoutService.isSidebarOpen();
  }

  toggleDrawer() {
    this.drawerOpen.set(!this.drawerOpen());
  }

  closeDrawer() {
    this.drawerOpen.set(false);
  }
}

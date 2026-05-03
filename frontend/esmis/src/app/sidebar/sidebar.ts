import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, inject, OnInit, signal } from '@angular/core';
import { RouterLink, RouterLinkActive } from "@angular/router";
import { AuthService } from '../../services/auth.service';
import { LayoutService } from '../../services/layout.service';
import { NotificationApiService } from '../../services/notification-api.service';
import { NotificationService } from '../../services/notification.service';

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
  public notifApiService = inject(NotificationApiService);
  public notifService = inject(NotificationService);
  private cdr = inject(ChangeDetectorRef);

  public isDropdownOpen = signal(localStorage.getItem('isDropdownOpen') === 'true');
  public connectionState = signal<'connected' | 'disconnected' | 'error'>('disconnected');

  ngOnInit() {
    // Initial count fetch 
    if (this.authService.isLoggedIn()) {
      this.notifApiService.getUnreadCount().subscribe();
    }

    // Listen for connection status with a small delay to prevent NG0100
    setTimeout(() => {
      this.notifService.connectionStatus$.subscribe(state => {
        this.connectionState.set(state);
        this.cdr.detectChanges();
      });
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

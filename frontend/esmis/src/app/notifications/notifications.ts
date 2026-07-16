import { ChangeDetectorRef, Component, inject, OnInit, signal, computed } from '@angular/core';
import { NotificationApiService } from '../../services/notification-api.service';
import { UserManagementService } from '../../services/user-management.service';
import { AuthService } from '../../services/auth.service';
import { Notification, Office, NotificationFilters } from '../../models/smis.model';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { NotificationService } from '../../services/notification.service';

@Component({
  selector: 'app-notifications',
  imports: [CommonModule, FormsModule],
  templateUrl: './notifications.html',
  styleUrl: './notifications.scss',
})
export class Notifications implements OnInit {
  notifApiService = inject(NotificationApiService);
  notifService = inject(NotificationService);
  userManagementService = inject(UserManagementService);
  authService = inject(AuthService);
  private cdr = inject(ChangeDetectorRef);
  
  notifications: Notification[] = [];
  offices: Office[] = [];
  isLoading = signal(true);
  currentPage = signal(1);
  lastPage = signal(1);
  activeTab = 'all';

  pageNumbers = computed(() => {
    const current = this.currentPage();
    const total = this.lastPage();
    return this.getVisiblePages(current, total);
  });

  getVisiblePages(current: number, total: number): (number | string)[] {
    const maxVisible = 5;
    if (total <= 7) {
      return Array.from({ length: total }, (_, i) => i + 1);
    }

    const pages: (number | string)[] = [];
    pages.push(1);

    let start = Math.max(2, current - 1);
    let end = Math.min(total - 1, current + 1);

    if (current <= 3) {
      end = 4;
    }
    if (current >= total - 2) {
      start = total - 3;
    }

    if (start > 2) {
      pages.push('...');
    }

    for (let i = start; i <= end; i++) {
      pages.push(i);
    }

    if (end < total - 1) {
      pages.push('...');
    }

    pages.push(total);
    return pages;
  }

  // Filter properties
  filters: NotificationFilters = {
    office_id: '',
    from_date: '',
    to_date: ''
  };

  ngOnInit() {
    // Small delay ensures Angular's initial check completes before we start loading
    setTimeout(() => {
      this.loadNotifications();
      this.loadOffices();
    });

    // Listen for real-time updates to the list
    this.notifService.notifications$.subscribe((newNotif: Notification) => {
      // Security check: Only show low stock / out of stock to admins/superadmins
      if ((newNotif.action === 'low stock' || newNotif.action === 'out of stock') && 
          !this.authService.hasRole('admin') && 
          !this.authService.hasRole('superadmin')) {
        return;
      }

      this.notifications.unshift(newNotif);
      // Remove last if we exceed 5 (optional, but consistent with pagination)
      if (this.notifications.length > 5) {
        this.notifications.pop();
      }
      this.cdr.detectChanges();
    });
  }

  loadNotifications(page: number = 1) {
    this.isLoading.set(true);
    this.currentPage.set(page);
    this.notifApiService.getNotifications(page, this.filters, this.activeTab).subscribe({
      next: (response) => {
        this.notifications = response.data;
        this.lastPage.set(response.last_page);
        this.isLoading.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.isLoading.set(false);
        this.cdr.detectChanges();
      }
    });
  }

  loadOffices() {
    this.userManagementService.listOffices().subscribe(data => {
      this.offices = data;
      this.cdr.detectChanges();
    });
  }

  applyFilters() {
    this.loadNotifications(1);
  }

  clearFilters() {
    this.filters = {
      office_id: '',
      from_date: '',
      to_date: ''
    };
    this.loadNotifications(1);
  }

  setTab(tab: string) {
    this.activeTab = tab;
    this.loadNotifications(1);
  }

  changePage(page: number) {
    if (page < 1 || page > this.lastPage()) return;
    this.loadNotifications(page);
  }

  getRelativeTime(dateString: string): string {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now.getTime() - date.getTime()) / 1000);

    if (diffInSeconds < 60) return 'Just now';
    
    const diffInMinutes = Math.floor(diffInSeconds / 60);
    if (diffInMinutes < 60) return `${diffInMinutes}m ago`;

    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) return `${diffInHours}h ago`;

    const diffInDays = Math.floor(diffInHours / 24);
    if (diffInDays < 7) return `${diffInDays}d ago`;

    const diffInWeeks = Math.floor(diffInDays / 7);
    if (diffInWeeks < 4) return `${diffInWeeks}w ago`;

    const diffInMonths = Math.floor(diffInDays / 30);
    if (diffInMonths < 12) return `${diffInMonths}mo ago`;

    const diffInYears = Math.floor(diffInDays / 365);
    return `${diffInYears}y ago`;
  }

  markAsRead(notification: Notification) {
    if (notification.read_at) return;
    
    this.notifApiService.markAsRead(notification.id).subscribe(() => {
      notification.read_at = new Date().toISOString();
    });
  }

  markAllAsRead() {
    this.notifApiService.markAllAsRead().subscribe(() => {
      this.notifications.forEach(n => n.read_at = new Date().toISOString());
    });
  }

  deleteNotification(event: Event, notification: Notification) {
    event.stopPropagation(); // Prevent triggering markAsRead from parent div
    this.notifApiService.deleteNotification(notification.id).subscribe(() => {
      // If the deleted notification was unread, decrement the global unread count
      if (!notification.read_at) {
        this.notifApiService.unreadCount.update(c => Math.max(0, c - 1));
      }
      this.notifications = this.notifications.filter(n => n.id !== notification.id);
      this.cdr.detectChanges();
    });
  }
}

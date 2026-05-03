import { ChangeDetectorRef, Component, inject, OnInit, signal } from '@angular/core';
import { Sidebar } from "../sidebar/sidebar";
import { TopNav } from "../top-nav/top-nav";
import { NotificationApiService } from '../../services/notification-api.service';
import { UserManagementService } from '../../services/user-management.service';
import { Notification, Office, NotificationFilters } from '../../models/smis.model';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { NotificationService } from '../../services/notification.service';

@Component({
  selector: 'app-notifications',
  imports: [Sidebar, TopNav, CommonModule, FormsModule],
  templateUrl: './notifications.html',
  styleUrl: './notifications.scss',
})
export class Notifications implements OnInit {
  notifApiService = inject(NotificationApiService);
  notifService = inject(NotificationService);
  userManagementService = inject(UserManagementService);
  private cdr = inject(ChangeDetectorRef);
  
  notifications: Notification[] = [];
  offices: Office[] = [];
  isLoading = signal(true);
  currentPage = 1;
  lastPage = 1;
  activeTab = 'all';

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
    this.currentPage = page;
    this.notifApiService.getNotifications(page, this.filters, this.activeTab).subscribe({
      next: (response) => {
        this.notifications = response.data;
        this.lastPage = response.last_page;
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
    if (page < 1 || page > this.lastPage) return;
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

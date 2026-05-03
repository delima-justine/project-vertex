import { ChangeDetectorRef, Component, inject, OnInit, signal } from '@angular/core';
import { Sidebar } from "../sidebar/sidebar";
import { TopNav } from "../top-nav/top-nav";
import { NotificationApiService } from '../../services/notification-api.service';
import { Notification } from '../../models/smis.model';
import { CommonModule } from '@angular/common';

import { NotificationService } from '../../services/notification.service';

@Component({
  selector: 'app-notifications',
  imports: [Sidebar, TopNav, CommonModule],
  templateUrl: './notifications.html',
  styleUrl: './notifications.scss',
})
export class Notifications implements OnInit {
  notifApiService = inject(NotificationApiService);
  notifService = inject(NotificationService);
  private cdr = inject(ChangeDetectorRef);
  
  notifications: Notification[] = [];
  isLoading = signal(true);

  ngOnInit() {
    // Small delay ensures Angular's initial check completes before we start loading
    setTimeout(() => {
      this.loadNotifications();
    });

    // Listen for real-time updates to the list
    this.notifService.notifications$.subscribe((newNotif: Notification) => {
      this.notifications.unshift(newNotif);
      this.cdr.detectChanges();
    });
  }

  loadNotifications() {
    this.isLoading.set(true);
    this.notifApiService.getNotifications().subscribe({
      next: (data) => {
        this.notifications = data;
        this.isLoading.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.isLoading.set(false);
        this.cdr.detectChanges();
      }
    });
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
}

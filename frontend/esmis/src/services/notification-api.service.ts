import { HttpClient } from '@angular/common/http';
import { effect, inject, Injectable, signal } from '@angular/core';
import { Notification } from '../models/smis.model';
import { tap } from 'rxjs';
import { NotificationService } from './notification.service';
import { AuthService } from './auth.service';

@Injectable({
  providedIn: 'root',
})
export class NotificationApiService {
  private http = inject(HttpClient);
  private notifService = inject(NotificationService);
  private authService = inject(AuthService);
  private apiUrl = 'http://localhost:8000/api';

  unreadCount = signal(0);
  private isListening = false;
  private currentUserId: number | null = null;

  constructor() {
    // Listen for real-time notifications once
    this.notifService.notifications$.subscribe(() => {
      this.unreadCount.update(c => c + 1);
    });

    // Automatically manage real-time connection based on auth state
    effect(() => {
      const user = this.authService.currentUser();
      
      if (user) {
        // If user is different or we aren't listening yet
        if (this.currentUserId !== user.id) {
          console.log('[NotificationApiService] User detected, initializing real-time...');
          if (this.currentUserId) {
            this.notifService.disconnect();
          }
          this.notifService.listenToNotifications(user.id);
          this.currentUserId = user.id;
          this.isListening = true;
        }
      } else {
        // User logged out
        if (this.isListening) {
          console.log('[NotificationApiService] User logged out, disconnecting real-time...');
          this.notifService.disconnect();
          this.currentUserId = null;
          this.isListening = false;
        }
      }
    });
  }

  getNotifications() {
    return this.http.get<Notification[]>(`${this.apiUrl}/notifications`);
  }

  getUnreadCount() {
    return this.http.get<{ count: number }>(`${this.apiUrl}/notifications/unread-count`).pipe(
      tap(res => this.unreadCount.set(res.count))
    );
  }

  markAsRead(notificationId: number) {
    return this.http.patch(`${this.apiUrl}/notifications/${notificationId}/read`, {}).pipe(
      tap(() => this.unreadCount.update(c => Math.max(0, c - 1)))
    );
  }

  markAllAsRead() {
    return this.http.post(`${this.apiUrl}/notifications/mark-all-read`, {}).pipe(
      tap(() => this.unreadCount.set(0))
    );
  }

  deleteNotification(notificationId: number) {
    return this.http.delete(`${this.apiUrl}/notifications/${notificationId}`).pipe(
      tap(() => {
        // We might want to refresh unread count if we deleted an unread notification
        // But for simplicity, we'll let the component handle the list update
      })
    );
  }
}

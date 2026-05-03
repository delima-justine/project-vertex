import { HttpClient } from '@angular/common/http';
import { inject, Injectable, signal } from '@angular/core';
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

  constructor() {
    // Listen for real-time notifications once
    this.notifService.notifications$.subscribe(() => {
      this.unreadCount.update(c => c + 1);
    });

    // Handle connection status changes if needed
    this.notifService.connectionStatus$.subscribe(status => {
      if (status === 'connected' && !this.isListening) {
        this.initRealTime();
      }
    });
  }

  initRealTime() {
    const user = this.authService.currentUser();
    if (user && !this.isListening) {
      this.notifService.listenToNotifications(user.id);
      this.isListening = true;
    }
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
}

import { HttpClient, HttpParams } from '@angular/common/http';
import { effect, inject, Injectable, signal } from '@angular/core';
import { Notification, PaginatedResponse, NotificationFilters } from '../models/smis.model';
import { tap } from 'rxjs';
import { NotificationService } from './notification.service';
import { AuthService } from './auth.service';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root',
})
export class NotificationApiService {
  private http = inject(HttpClient);
  private notifService = inject(NotificationService);
  private authService = inject(AuthService);
  private apiUrl = environment.apiUrl;

  unreadCount = signal(0);
  private isListening = false;
  private currentUserId: number | null = null;

  constructor() {
    // Listen for real-time notifications once
    this.notifService.notifications$.subscribe((notif: Notification) => {
      // Only increment and notify if it's a regular notification 
      // OR if it's a low stock / out of stock notification and the user is an admin/superadmin
      if ((notif.action !== 'low stock' && notif.action !== 'out of stock') || this.authService.hasRole('admin') || this.authService.hasRole('superadmin')) {
        this.unreadCount.update(c => c + 1);
      }
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

  getNotifications(page: number = 1, filters: NotificationFilters = {}, tab: string = 'all') {
    let params = new HttpParams()
      .set('page', page.toString())
      .set('tab', tab);
    
    if (filters.office_id) params = params.set('office_id', filters.office_id.toString());
    if (filters.from_date) params = params.set('from_date', filters.from_date);
    if (filters.to_date) params = params.set('to_date', filters.to_date);

    return this.http.get<PaginatedResponse<Notification>>(`${this.apiUrl}/notifications`, { params });
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

import { Injectable } from '@angular/core';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { BehaviorSubject, Subject } from 'rxjs';
import { environment } from '../environments/environment';

(window as any).Pusher = Pusher;

@Injectable({
  providedIn: 'root',
})
export class NotificationService {
  private echo: Echo<any> | null = null;
  private notificationsSubject = new Subject<any>();
  public notifications$ = this.notificationsSubject.asObservable();

  private connectionStatusSubject = new BehaviorSubject<'connected' | 'disconnected' | 'error'>('disconnected');
  public connectionStatus$ = this.connectionStatusSubject.asObservable();

  private getEcho(): Echo<any> {
    const token = localStorage.getItem('auth_token');
    
    if (!this.echo) {
      console.log('[NotificationService] Initializing Echo...');
      this.echo = new Echo({
        broadcaster: 'pusher',
        key: environment.pusherKey,
        cluster: environment.pusherCluster,
        forceTLS: true, // Use true for secure 'wss' connection to hosted Pusher
        authEndpoint: `${environment.apiUrl}/broadcasting/auth`,
        auth: {
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: 'application/json',
          },
        },
      });

      // Connection state listeners
      this.echo.connector.pusher.connection.bind('connected', () => {
        console.log('%c[NotificationService] WebSocket Connected ✅', 'color: #28a745; font-weight: bold;');
        this.connectionStatusSubject.next('connected');
      });

      this.echo.connector.pusher.connection.bind('disconnected', () => {
        console.warn('[NotificationService] WebSocket Disconnected ❌');
        this.connectionStatusSubject.next('disconnected');
      });

      this.echo.connector.pusher.connection.bind('error', (err: any) => {
        console.error('[NotificationService] WebSocket Error ⚠️', err);
        this.connectionStatusSubject.next('error');
      });
    }
    return this.echo;
  }

  listenToNotifications(userId: number) {
    const echo = this.getEcho();
    echo.private(`App.Models.User.${userId}`)
      .listen('.notification.sent', (data: any) => {
        this.notificationsSubject.next(data.notification);
      });
  }

  disconnect() {
    if (this.echo) {
      this.echo.disconnect();
      this.echo = null;
    }
  }
}

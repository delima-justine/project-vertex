import { Injectable, inject, NgZone } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from './auth.service';
import { ToastService } from './toast.service';
import { fromEvent, merge, Subscription, timer } from 'rxjs';
import { switchMap, tap } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class AutoLogoutService {
  private authService = inject(AuthService);
  private router = inject(Router);
  private toastService = inject(ToastService);
  private ngZone = inject(NgZone);

  private readonly INACTIVITY_TIMEOUT = 5 * 60 * 1000; // 5 minutes in milliseconds
  private activitySubscription?: Subscription;

  constructor() {
    this.initListener();
  }

  private initListener() {
    // Monitor user activity and reset timer
    this.ngZone.runOutsideAngular(() => {
      const activityEvents$ = merge(
        fromEvent(window, 'mousemove'),
        fromEvent(window, 'keydown'),
        fromEvent(window, 'click'),
        fromEvent(window, 'touchstart'),
        fromEvent(window, 'scroll')
      );

      this.activitySubscription = activityEvents$.pipe(
        switchMap(() => {
          // If logged in, start/reset inactivity timer
          if (this.authService.isLoggedIn()) {
            return timer(this.INACTIVITY_TIMEOUT);
          }
          return [];
        })
      ).subscribe(() => {
        // When timer expires, run logout in NgZone
        this.ngZone.run(() => {
          this.logoutUser();
        });
      });
    });
  }

  private logoutUser() {
    if (this.authService.isLoggedIn()) {
      this.authService.logout().subscribe({
        next: () => {
          this.toastService.info('Session expired due to inactivity.');
          this.router.navigate(['/']);
        },
        error: () => {
          // Fallback if API logout fails
          localStorage.removeItem('auth_token');
          this.authService.currentUser.set(null);
          this.router.navigate(['/']);
        }
      });
    }
  }

  stopMonitoring() {
    this.activitySubscription?.unsubscribe();
  }
}

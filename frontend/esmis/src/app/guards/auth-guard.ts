import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { toObservable } from '@angular/core/rxjs-interop';
import { filter, map, take } from 'rxjs';

export const authGuard: CanActivateFn = (route, state) => {
  const authService = inject(AuthService);
  const router = inject(Router);

  if (!authService.isLoggedIn()) {
    return router.parseUrl('/');
  }

  return toObservable(authService.initialized).pipe(
    filter(initialized => initialized),
    take(1),
    map(() => {
      if(authService.isLoggedIn()) {
        return true;
      } else {
        return router.parseUrl('/');
      }
    })
  );
};

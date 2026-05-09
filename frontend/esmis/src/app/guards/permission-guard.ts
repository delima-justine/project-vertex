import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { toObservable } from '@angular/core/rxjs-interop';
import { filter, map, take } from 'rxjs';

export const permissionGuard: CanActivateFn = (route, state) => {
  const authService = inject(AuthService);
  const router = inject(Router);

  if (!authService.isLoggedIn()) {
    return router.parseUrl('/');
  }

  return toObservable(authService.initialized).pipe(
    filter(initialized => initialized),
    take(1),
    map(() => {
      const requiredPermissions = route.data['permissions'] as string[];
      const requiredRole = route.data['role'] as string;

      if (requiredRole && !authService.hasRole(requiredRole)) {
        return router.parseUrl('/**');
      }

      if (requiredPermissions && requiredPermissions.length > 0) {
        const hasPermission = authService.hasAnyPermission(requiredPermissions);
        if (!hasPermission) {
          return router.parseUrl('/**');
        }
      }

      return true;
    })
  );
};

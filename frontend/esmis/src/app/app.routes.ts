import { Routes } from '@angular/router';
import { guestGuard } from './guards/guest-guard';
import { authGuard } from './guards/auth-guard';

export const routes: Routes = [
  {
    path: '', 
    loadComponent: () => import('./authentication/authentication')
      .then(m => m.Authentication),
    title: 'Login to SMIS',
    canActivate: [guestGuard]
  },
  {
    path: 'home',
    loadComponent: () => import('./home/home').then(m => m.Home),
    title: 'Dashboard',
    canActivate: [authGuard]
  },
  {
    path: 'admin/user-management',
    loadComponent: () => import('./user-management/user-management')
      .then(m => m.UserManagement),
    title: 'User Management',
    canActivate: [authGuard]

  },
  {
    path: 'notifications',
    loadComponent: () => import('./notifications/notifications')
      .then(m => m.Notifications),
    title: 'Notifications',
    canActivate: [authGuard]
  }, 
  {
    path: 'reports',
    loadComponent: () => import('./reports/reports').then(m => m.Reports),
    title: 'Reports',
    canActivate: [authGuard]
  },
  {
    path: 'create-request',
    loadComponent: () => import('./create-request/create-request')
      .then(m => m.CreateRequest),
    title: 'Create Request',
    canActivate: [authGuard]
  },
  {
    path: 'pending-requests',
    loadComponent: () => import('./pending/pending').then(m => m.Pending),
    title: 'Pending Requests',
    canActivate: [authGuard]
  },
  {
    path: 'approved-requests',
    loadComponent: () => import('./approved/approved').then(m => m.Approved),
    title: 'Approved Requests',
    canActivate: [authGuard]
  },
  {
    path: 'released-requests',
    loadComponent: () => import('./released/released').then(m => m.Released),
    title: 'Released Requests',
    canActivate: [authGuard]
  },
  {
    path: 'disapproved-requests',
    loadComponent: () => import('./disapproved/disapproved')
      .then(m => m.Disapproved),
    title: 'Disapproved Requests',
    canActivate: [authGuard]
  },
  {
    path: 'help',
    loadComponent: () => import('./help-and-support/help-and-support')
      .then(m => m.HelpAndSupport),
    title: 'Help and Support',
    canActivate: [authGuard]
  }
];

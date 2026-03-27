import { Routes } from '@angular/router';

export const routes: Routes = [
  {
    path: '', 
    loadComponent: () => import('./authentication/authentication')
      .then(m => m.Authentication),
    title: 'Login to SMIS'
  },
  {
    path: 'admin/home',
    loadComponent: () => import('./home/home').then(m => m.Home),
    title: 'Admin Dashboard'
  },
  {
    path: 'admin/user-management',
    loadComponent: () => import('./user-management/user-management')
      .then(m => m.UserManagement),
    title: 'User Management'
  },
  {
    path: 'admin/notifications',
    loadComponent: () => import('./notifications/notifications')
      .then(m => m.Notifications),
    title: 'Notifications'
  }, 
  {
    path: 'admin/reports',
    loadComponent: () => import('./reports/reports').then(m => m.Reports),
    title: 'Reports'
  },
  {
    path: 'create-request',
    loadComponent: () => import('./create-request/create-request')
      .then(m => m.CreateRequest),
    title: 'Create Request'
  },
  {
    path: 'pending-requests',
    loadComponent: () => import('./pending/pending').then(m => m.Pending),
    title: 'Pending Requests'
  },
  {
    path: 'approved-requests',
    loadComponent: () => import('./approved/approved').then(m => m.Approved),
    title: 'Approved Requests'
  },
  {
    path: 'released-requests',
    loadComponent: () => import('./released/released').then(m => m.Released),
    title: 'Released Requests'
  }
];

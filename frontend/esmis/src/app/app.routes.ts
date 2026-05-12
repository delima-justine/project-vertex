import { Routes } from '@angular/router';
import { guestGuard } from './guards/guest-guard';
import { authGuard } from './guards/auth-guard';
import { permissionGuard } from './guards/permission-guard';
import { MainLayout } from './layout/main-layout';

export const routes: Routes = [
  {
    path: '', 
    loadComponent: () => import('./authentication/authentication')
      .then(m => m.Authentication),
    title: 'Login to SMIS',
    canActivate: [guestGuard]
  },
  {
    path: 'forgot-pass',
    loadComponent: () => import('./forgot-password/forgot-password')
      .then(m => m.ForgotPassword),
    title: 'Forgot Password',
  },
  {
    path: 'reset-password',
    loadComponent: () => import('./reset-password/reset-password')
      .then(m => m.ResetPassword),
    title: 'Reset Password',
  },
  {
    path: '',
    component: MainLayout,
    canActivate: [authGuard],
    children: [
      {
        path: 'home',
        loadComponent: () => import('./home/home').then(m => m.Home),
        title: 'Dashboard'
      },
      {
        path: 'admin/user-management',
        loadComponent: () => import('./user-management/user-management')
          .then(m => m.UserManagement),
        title: 'User Management',
        canActivate: [permissionGuard],
        data: { permissions: ['add_user', 'edit_user', 'delete_user'] }
      },
      {
        path: 'notifications',
        loadComponent: () => import('./notifications/notifications')
          .then(m => m.Notifications),
        title: 'Notifications'
      }, 
      {
        path: 'reports',
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
        title: 'Pending Requests',
        canActivate: [permissionGuard],
        data: { permissions: ['view_pending_requests'] }
      },
      {
        path: 'approved-requests',
        loadComponent: () => import('./approved/approved').then(m => m.Approved),
        title: 'Approved Requests',
        canActivate: [permissionGuard],
        data: { permissions: ['view_approved_requests'] }
      },
      {
        path: 'released-requests',
        loadComponent: () => import('./released/released').then(m => m.Released),
        title: 'Released Requests',
        canActivate: [permissionGuard],
        data: { permissions: ['view_released_requests'] }
      },
      {
        path: 'disapproved-requests',
        loadComponent: () => import('./disapproved/disapproved')
          .then(m => m.Disapproved),
        title: 'Disapproved Requests',
        canActivate: [permissionGuard],
        data: { permissions: ['view_disapproved_requests'] }
      },
      {
        path: 'help',
        loadComponent: () => import('./help-and-support/help-and-support')
          .then(m => m.HelpAndSupport),
        title: 'Help and Support'
      },
      {
        path: 'requests/edit-ris/:id',
        loadComponent: () => import('./edit-ris/edit-ris')
          .then(m => m.EditRis),
        title: 'Edit RIS',
        canActivate: [permissionGuard],
        data: { permissions: ['edit_ris'] }
      }
    ]
  },
  {
    path: '**',
    loadComponent: () => import('./not-found/not-found').then(m => m.NotFound),
    title: '404 - Not Found'
  }
];

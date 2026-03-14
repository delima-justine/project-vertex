import { Routes } from '@angular/router';

export const routes: Routes = [
  {
    path: '', 
    loadComponent: () => import('./authentication/authentication').then(m => m.Authentication)
  },
  {
    path: 'admin/home',
    loadComponent: () => import('./home/home').then(m => m.Home)
  }
];

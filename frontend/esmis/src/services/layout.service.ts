import { Injectable, signal, effect, WritableSignal } from '@angular/core';

@Injectable({
  providedIn: 'root',
})
export class LayoutService {
  isSidebarOpen: WritableSignal<boolean> = signal(
    window.innerWidth > 991 ? localStorage.getItem('isSidebarOpen') !== 'false' : false
  );

  constructor() {
    effect(() => {
      if (this.isSidebarOpen()) {
        document.body.classList.remove('sidebar-collapsed');
        if (window.innerWidth <= 991) {
          document.body.style.overflow = 'hidden';
        }
      } else {
        document.body.classList.add('sidebar-collapsed');
        document.body.style.overflow = '';
      }
    });
  }

  toggleSidebar() {
    const newState = !this.isSidebarOpen();
    localStorage.setItem('isSidebarOpen', String(newState));
    this.isSidebarOpen.set(newState);
  }
}

import { Injectable, signal, effect, WritableSignal } from '@angular/core';

@Injectable({
  providedIn: 'root',
})
export class LayoutService {
  isSidebarOpen: WritableSignal<boolean> = signal(localStorage.getItem('isSidebarOpen') !== 'false');

  constructor() {
    effect(() => {
      if (this.isSidebarOpen()) {
        document.body.classList.remove('sidebar-collapsed');
      } else {
        document.body.classList.add('sidebar-collapsed');
      }
    });
  }

  toggleSidebar() {
    const newState = !this.isSidebarOpen();
    localStorage.setItem('isSidebarOpen', String(newState));
    this.isSidebarOpen.set(newState);
  }
}

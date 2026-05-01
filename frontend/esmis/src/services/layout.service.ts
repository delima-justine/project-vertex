import { Injectable, signal } from '@angular/core';

@Injectable({
  providedIn: 'root',
})
export class LayoutService {
  private isSidebarOpen = signal(false);

  toggleSidebar() {
    localStorage.setItem('isSidebarOpen', String(!this.isSidebarOpen()));
    this.isSidebarOpen.set(!this.isSidebarOpen());
  }

  getSidebarState() {
    const storedState = localStorage.getItem('isSidebarOpen');
    return storedState === 'true' ? true : false;
  }
}

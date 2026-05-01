import { Component, signal } from '@angular/core';

@Component({
  selector: 'app-top-nav',
  imports: [],
  templateUrl: './top-nav.html',
  styleUrl: './top-nav.scss',
})
export class TopNav {
  isOpen = signal(false);
  
  toggleDropdown() {
    this.isOpen.set(!this.isOpen());
  }

  logout() {
    console.log('User logged out');
  }
}

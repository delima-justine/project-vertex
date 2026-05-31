import { Component, signal, inject, HostListener } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { NgxLoadingBar } from '@ngx-loading-bar/core';
import { ToastContainerComponent } from './toast-container/toast-container';
import { ConfirmModalComponent } from './confirm-modal/confirm-modal';
import { ThemeService } from '../services/theme';
import { AutoLogoutService } from '../services/auto-logout';
import { UpdateService } from '../services/update.service';

@Component({
  selector: 'app-root',
  imports: [RouterOutlet, NgxLoadingBar, ToastContainerComponent, ConfirmModalComponent],
  templateUrl: './app.html',
  styleUrl: './app.scss'
})
export class App {
  protected readonly title = signal('esmis');
  protected readonly isOffline = signal(!navigator.onLine);

  private themeService = inject(ThemeService);
  private autoLogoutService = inject(AutoLogoutService);
  private updateService = inject(UpdateService);

  @HostListener('window:offline')
  setOffline() {
    this.isOffline.set(true);
  }

  @HostListener('window:online')
  setOnline() {
    this.isOffline.set(false);
  }
}

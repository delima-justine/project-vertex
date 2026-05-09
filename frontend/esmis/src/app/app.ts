import { Component, signal } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { NgxLoadingBar } from '@ngx-loading-bar/core';
import { ToastContainerComponent } from './toast-container/toast-container';
import { ConfirmModalComponent } from './confirm-modal/confirm-modal';

@Component({
  selector: 'app-root',
  imports: [RouterOutlet, NgxLoadingBar, ToastContainerComponent, ConfirmModalComponent],
  templateUrl: './app.html',
  styleUrl: './app.scss'
})
export class App {
  protected readonly title = signal('esmis');
}

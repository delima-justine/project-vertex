import { Component, inject } from '@angular/core';
import { NgClass } from '@angular/common';
import { ToastService, ToastInfo } from '../../services/toast.service';

@Component({
  selector: 'app-toast-container',
  standalone: true,
  imports: [NgClass],
  templateUrl: './toast-container.html',
  styleUrl: './toast-container.scss',
  host: { class: 'toast-container position-fixed bottom-0 end-0 p-3', style: 'z-index: 1200' }
})
export class ToastContainerComponent {
  toastService = inject(ToastService);

  remove(toast: ToastInfo) {
    this.toastService.remove(toast);
  }
}

import { Injectable, signal } from '@angular/core';

export interface ToastInfo {
  header?: string;
  body: string;
  delay?: number;
  classname?: string;
  action?: {
    label: string;
    onClick: () => void;
  };
}

@Injectable({ providedIn: 'root' })
export class ToastService {
  toasts = signal<ToastInfo[]>([]);

  show(body: string, options: Partial<ToastInfo> = {}) {
    const toast: ToastInfo = { body, ...options };
    this.toasts.update((toasts) => [...toasts, toast]);

    // If an action is provided, we default to persistent (delay: 0) unless specified otherwise
    const delay = toast.action ? (toast.delay ?? 0) : (toast.delay ?? 5000);

    if (delay !== 0) {
      setTimeout(() => this.remove(toast), delay);
    }
  }

  success(body: string, header: string = 'Success') {
    this.show(body, { header, classname: 'bg-success text-light' });
  }

  error(body: string, header: string = 'Error') {
    this.show(body, { header, classname: 'bg-danger text-light' });
  }

  warning(body: string, header: string = 'Warning') {
    this.show(body, { header, classname: 'bg-warning text-dark' });
  }

  info(body: string, header: string = 'Information') {
    this.show(body, { header, classname: 'bg-info text-light' });
  }

  remove(toast: ToastInfo) {
    this.toasts.update((toasts) => toasts.filter((t) => t !== toast));
  }

  clear() {
    this.toasts.set([]);
  }
}

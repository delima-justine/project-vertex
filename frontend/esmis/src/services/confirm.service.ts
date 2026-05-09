import { Injectable, signal } from '@angular/core';

export interface ConfirmOptions {
  title?: string;
  confirmText?: string;
  cancelText?: string;
  danger?: boolean;
}

@Injectable({ providedIn: 'root' })
export class ConfirmService {
  private resolve?: (value: boolean) => void;

  state = signal<{
    title: string;
    message: string;
    confirmText: string;
    cancelText: string;
    danger: boolean;
    isOpen: boolean;
  }>({
    title: 'Confirm',
    message: '',
    confirmText: 'Confirm',
    cancelText: 'Cancel',
    danger: false,
    isOpen: false,
  });

  confirm(message: string, options: ConfirmOptions = {}): Promise<boolean> {
    this.state.set({
      title: options.title || 'Confirm Action',
      message,
      confirmText: options.confirmText || 'Confirm',
      cancelText: options.cancelText || 'Cancel',
      danger: options.danger || false,
      isOpen: true,
    });

    return new Promise((resolve) => {
      this.resolve = resolve;
    });
  }

  handleResult(result: boolean) {
    this.state.update((s) => ({ ...s, isOpen: false }));
    if (this.resolve) {
      this.resolve(result);
      this.resolve = undefined;
    }
  }
}

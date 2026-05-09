import { Component, inject, ViewChild, ElementRef, AfterViewInit, effect } from '@angular/core';
import { ConfirmService } from '../../services/confirm.service';

@Component({
  selector: 'app-confirm-modal',
  standalone: true,
  templateUrl: './confirm-modal.html',
})
export class ConfirmModalComponent implements AfterViewInit {
  confirmService = inject(ConfirmService);

  @ViewChild('confirmModal') modalElement!: ElementRef;
  private modalInstance: any;

  constructor() {
    effect(() => {
      const state = this.confirmService.state();
      if (this.modalInstance) {
        if (state.isOpen) {
          this.modalInstance.show();
        } else {
          this.modalInstance.hide();
        }
      }
    });
  }

  ngAfterViewInit() {
    const bootstrap = (window as any).bootstrap;
    this.modalInstance = new bootstrap.Modal(this.modalElement.nativeElement, {
      backdrop: 'static',
      keyboard: false
    });

    // Handle manual close (e.g. if someone uses the JS API elsewhere)
    this.modalElement.nativeElement.addEventListener('hidden.bs.modal', () => {
      if (this.confirmService.state().isOpen) {
        this.confirmService.handleResult(false);
      }
    });
  }

  onConfirm() {
    this.confirmService.handleResult(true);
  }

  onCancel() {
    this.confirmService.handleResult(false);
  }
}

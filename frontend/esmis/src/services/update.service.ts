import { Injectable, inject } from '@angular/core';
import { SwUpdate, VersionReadyEvent } from '@angular/service-worker';
import { filter } from 'rxjs';
import { ToastService } from './toast.service';

@Injectable({
  providedIn: 'root',
})
export class UpdateService {
  private swUpdate = inject(SwUpdate);
  private toastService = inject(ToastService);

  constructor() {
    if (this.swUpdate.isEnabled) {
      this.swUpdate.versionUpdates
        .pipe(filter((evt): evt is VersionReadyEvent => evt.type === 'VERSION_READY'))
        .subscribe(() => {
          this.toastService.show('A new version of SMIS is available.', {
            header: 'Update Available',
            classname: 'bg-primary text-light',
            action: {
              label: 'Reload',
              onClick: () => document.location.reload(),
            },
          });
        });
    }
  }
}

import { Component, inject, OnInit, signal } from '@angular/core';
import { SettingsService } from '../../services/settings.service';

@Component({
  selector: 'app-rate-our-system',
  standalone: true,
  imports: [],
  templateUrl: './rate-our-system.html',
  styleUrl: './rate-our-system.scss',
})
export class RateOurSystem implements OnInit {
  private settingsService = inject(SettingsService);

  public isFormReady = signal(false);
  public formUrl = signal('');

  ngOnInit() {
    this.settingsService.getSettings().subscribe({
      next: (data) => {
        this.isFormReady.set(data.is_form_ready === true || data.is_form_ready === '1' || data.is_form_ready === 'true');
        this.formUrl.set(data.form_url || '');
      }
    });
  }

  openForm(): void {
    if (this.formUrl()) {
      window.open(this.formUrl(), '_blank');
    }
  }
}

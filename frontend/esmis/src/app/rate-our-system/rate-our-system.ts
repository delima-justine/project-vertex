import { Component, signal } from '@angular/core';

@Component({
  selector: 'app-rate-our-system',
  standalone: true,
  imports: [],
  templateUrl: './rate-our-system.html',
  styleUrl: './rate-our-system.scss',
})
export class RateOurSystem {
  // ============================================================
  // CONFIGURATION — Change these values when forms are ready
  // ============================================================
  public isFormReady = signal(false);
  public formUrl = signal('');
  // ============================================================

  openForm(): void {
    window.open(this.formUrl(), '_blank');
  }
}

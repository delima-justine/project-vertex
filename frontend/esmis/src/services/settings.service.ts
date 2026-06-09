import { HttpClient } from '@angular/common/http';
import { inject, Injectable, signal } from '@angular/core';
import { Observable, tap } from 'rxjs';
import { environment } from '../environments/environment';
import { SystemSettings } from '../models/smis.model';

@Injectable({
  providedIn: 'root',
})
export class SettingsService {
  private http = inject(HttpClient);
  private apiUrl = environment.apiUrl;

  settings = signal<SystemSettings | null>(null);

  getSettings(): Observable<SystemSettings> {
    return this.http.get<SystemSettings>(`${this.apiUrl}/settings`).pipe(
      tap(data => this.settings.set(data))
    );
  }

  updateSettings(payload: Partial<SystemSettings>): Observable<{ message: string }> {
    return this.http.put<{ message: string }>(`${this.apiUrl}/settings`, payload).pipe(
      tap(() => {
        // Refresh local signal if we have current settings
        const current = this.settings();
        if (current) {
          this.settings.set({ ...current, ...payload });
        }
      })
    );
  }
}

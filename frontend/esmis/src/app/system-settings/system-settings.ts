import { Component, inject, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { SettingsService, SystemSettings } from '../../services/settings.service';
import { ToastService } from '../../services/toast.service';
import { environment } from '../../environments/environment';

@Component({
  selector: 'app-system-settings',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './system-settings.html',
  styleUrl: './system-settings.scss'
})
export class SystemSettingsComponent implements OnInit {
  private settingsService = inject(SettingsService);
  private toastService = inject(ToastService);
  public env = environment;

  settings = signal<SystemSettings>({
    director_name: '',
    director_title: '',
    custodian_name: '',
    custodian_title: ''
  });

  isLoading = signal(true);
  isSaving = signal(false);

  ngOnInit() {
    this.loadSettings();
  }

  loadSettings() {
    this.isLoading.set(true);
    this.settingsService.getSettings().subscribe({
      next: (data) => {
        // Merge with environment defaults if values are missing
        this.settings.set({
          director_name: data.director_name || this.env.identities.directorName,
          director_title: data.director_title || this.env.identities.directorTitle,
          custodian_name: data.custodian_name || this.env.identities.custodianName,
          custodian_title: data.custodian_title || this.env.identities.custodianTitle
        });
        this.isLoading.set(false);
      },
      error: (err) => {
        console.error('Error loading settings', err);
        this.toastService.error('Failed to load system settings.');
        this.isLoading.set(false);
      }
    });
  }

  saveSettings() {
    this.isSaving.set(true);
    this.settingsService.updateSettings(this.settings()).subscribe({
      next: () => {
        this.toastService.success('System settings updated successfully!');
        this.isSaving.set(false);
      },
      error: (err) => {
        console.error('Error saving settings', err);
        this.toastService.error('Failed to save system settings.');
        this.isSaving.set(false);
      }
    });
  }
}

import 'zone.js/testing';
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { provideHttpClient } from '@angular/common/http';
import { provideHttpClientTesting } from '@angular/common/http/testing';
import { provideRouter } from '@angular/router';
import { of } from 'rxjs';

import { SystemSettingsComponent } from './system-settings';
import { SettingsService } from '../../services/settings.service';

describe('SystemSettingsComponent', () => {
  let component: SystemSettingsComponent;
  let fixture: ComponentFixture<SystemSettingsComponent>;
  let settingsService: SettingsService;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SystemSettingsComponent],
      providers: [
        provideHttpClient(),
        provideHttpClientTesting(),
        provideRouter([]),
        {
          provide: SettingsService,
          useValue: {
            getSettings: () => of({
              director_name: 'TEST DIRECTOR',
              director_title: 'TEST TITLE',
              custodian_name: 'TEST CUSTODIAN',
              custodian_title: 'TEST CUSTODIAN TITLE'
            }),
            updateSettings: () => of({ message: 'Success' })
          }
        }
      ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SystemSettingsComponent);
    component = fixture.componentInstance;
    settingsService = TestBed.inject(SettingsService);
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  it('should load settings on init', () => {
    expect(component.settings().director_name).toBe('TEST DIRECTOR');
    expect(component.isLoading()).toBeFalse();
  });

  it('should call updateSettings when saveSettings is called', () => {
    const updateSpy = spyOn(settingsService, 'updateSettings').and.callThrough();
    component.saveSettings();
    expect(updateSpy).toHaveBeenCalled();
    expect(component.isSaving()).toBeFalse();
  });
});

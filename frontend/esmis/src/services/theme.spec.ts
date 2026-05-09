import { TestBed } from '@angular/core/testing';
import { ThemeService } from './theme';

describe('ThemeService', () => {
  let service: ThemeService;

  beforeEach(() => {
    localStorage.clear();
    TestBed.configureTestingModule({
      providers: [ThemeService]
    });
    service = TestBed.inject(ThemeService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });

  it('should toggle theme', () => {
    const initialTheme = service.theme();
    service.toggleTheme();
    expect(service.theme()).not.toBe(initialTheme);
  });

  it('should persist theme to localStorage', () => {
    service.toggleTheme();
    TestBed.flushEffects(); // Explicitly flush effects to ensure localStorage is updated
    const currentTheme = service.theme();
    expect(localStorage.getItem('theme-preference')).toBe(currentTheme);
  });
});

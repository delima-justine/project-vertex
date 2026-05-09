import { Injectable, signal, effect } from '@angular/core';

@Injectable({
  providedIn: 'root',
})
export class ThemeService {
  private readonly THEME_KEY = 'theme-preference';
  theme = signal<'light' | 'dark'>(this.getStoredTheme());

  constructor() {
    // Automatically apply the theme to the document whenever the signal changes
    effect(() => {
      const currentTheme = this.theme();
      localStorage.setItem(this.THEME_KEY, currentTheme);
      document.documentElement.setAttribute('data-bs-theme', currentTheme);
    });
  }

  private getStoredTheme(): 'light' | 'dark' {
    const storedTheme = localStorage.getItem(this.THEME_KEY);
    if (storedTheme === 'light' || storedTheme === 'dark') {
      return storedTheme;
    }
    // Default to system preference if no stored preference exists
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  toggleTheme() {
    this.theme.update((current) => (current === 'light' ? 'dark' : 'light'));
  }
}

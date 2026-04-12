import { HttpClient } from '@angular/common/http';
import { inject, Injectable, signal } from '@angular/core';
import { tap } from 'rxjs';
import { User } from '../models/smis.model';

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private http = inject(HttpClient);
  private apiUrl = 'http://localhost:8000/api';

  // Use a signal to track auth state
  currentUser = signal<User | null>(null);

  constructor() {
    // If we have a token, fetch the user data on initialization
    if (this.isLoggedIn()) {
      this.getUser().subscribe();
    }
  }

  login(credentials: any) {
    return this.http.post(`${this.apiUrl}/login`, credentials).pipe(
      tap((response: any) => {
        localStorage.setItem('auth_token', response.token);
        this.getUser().subscribe();
      })
    );
  }

  // New method to fetch user profile with role
  getUser() {
    return this.http.get<User>(`${this.apiUrl}/user`).pipe(
      tap((user: User) => {
        this.currentUser.set(user);
      })
    );
  }

  logout() {
    return this.http.post(`${this.apiUrl}/logout`, {}).pipe(
      tap(() => {
        localStorage.removeItem('auth_token');
        this.currentUser.set(null);
      })
    );
  }

  getToken() {
    return localStorage.getItem('auth_token');
  }

  isLoggedIn(): boolean {
    return !!this.getToken();
  }
}

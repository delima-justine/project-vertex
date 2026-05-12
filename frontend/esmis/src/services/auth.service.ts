import { HttpClient } from '@angular/common/http';
import { inject, Injectable, signal } from '@angular/core';
import { tap } from 'rxjs';
import { AuthResponse, ChangePasswordPayload, CheckResetTokenPayload, CheckResetTokenResponse, ForgotPasswordPayload, GeneralResponse, LoginCredentials, ProfileResponse, ResetPasswordPayload, User } from '../models/smis.model';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private http = inject(HttpClient);
  private apiUrl = environment.production ? environment.apiUrl : 'http://localhost:8000/api';

  // Use a signal to track auth state
  currentUser = signal<User | null>(null);
  userPermissions = signal<string[]>([]);
  initialized = signal<boolean>(false);

  constructor() {
    // If we have a token, fetch the user data on initialization
    if (this.isLoggedIn()) {
      this.getUser().subscribe({
        next: () => this.initialized.set(true),
        error: () => {
          this.initialized.set(true);
          localStorage.removeItem('auth_token');
        }
      });
    } else {
      this.initialized.set(true);
    }
  }

  login(credentials: LoginCredentials) {
    return this.http.post<AuthResponse>(`${this.apiUrl}/login`, credentials).pipe(
      tap((response: AuthResponse) => {
        localStorage.setItem('auth_token', response.token);
        this.currentUser.set(response.user);
        this.userPermissions.set(response.permissions || []);
        console.log('AuthService: User permissions loaded:', response.permissions);
      })
    );
  }

  // New method to fetch user profile with role
  getUser() {
    return this.http.get<ProfileResponse>(`${this.apiUrl}/user/profile`).pipe(
      tap((response: ProfileResponse) => {
        this.currentUser.set(response.user);
        this.userPermissions.set(response.permissions || []);
        console.log('AuthService: User permissions loaded:', response.permissions);
      })
    );
  }

  hasPermission(permissionName: string): boolean {
    return this.userPermissions().includes(permissionName);
  }

  hasRole(roleName: string): boolean {
    const user = this.currentUser();
    if (!user || !user.role) return false;
    return user.role.role_name.toLowerCase() === roleName.toLowerCase();
  }

  hasAnyPermission(permissionNames: string[]): boolean {
    return permissionNames.some(p => this.userPermissions().includes(p));
  }

  logout() {
    return this.http.post<GeneralResponse>(`${this.apiUrl}/logout`, {}).pipe(
      tap(() => {
        localStorage.removeItem('auth_token');
        this.currentUser.set(null);
      })
    );
  }

  changePassword(data: ChangePasswordPayload) {
    return this.http.post<GeneralResponse>(`${this.apiUrl}/user/change-password`, data);
  }

  forgotPassword(data: ForgotPasswordPayload) {
    return this.http.post<GeneralResponse>(`${this.apiUrl}/forgot-password`, data);
  }

  checkResetToken(data: CheckResetTokenPayload) {
    return this.http.post<CheckResetTokenResponse>(`${this.apiUrl}/check-reset-token`, data);
  }

  resetPassword(data: ResetPasswordPayload) {
    return this.http.post<GeneralResponse>(`${this.apiUrl}/reset-password`, data);
  }

  getToken() {
    return localStorage.getItem('auth_token');
  }

  isLoggedIn(): boolean {
    return !!this.getToken();
  }
}

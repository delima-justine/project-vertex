import { HttpClient } from '@angular/common/http';
import { inject, Injectable, signal } from '@angular/core';
import { Observable, of, tap } from 'rxjs';
import { User, Office, Role, Permission } from '../models/smis.model';
import { environment } from '../environments/environment';

export type PaginatedUsers = {
  data: User[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
};

export type UserPayload = {
  first_name: string;
  middle_initial?: string;
  last_name: string;
  email: string;
  password?: string;
  role_id: number;
  office_id: number;
  permission_ids?: number[];
};

@Injectable({
  providedIn: 'root',
})
export class UserManagementService {
  private http = inject(HttpClient);
  private apiUrl = environment.apiUrl;

  private officesCache = signal<Office[] | null>(null);
  private rolesCache = signal<Role[] | null>(null);
  private permissionsCache = signal<Permission[] | null>(null);

  listOffices(): Observable<Office[]> {
    if (this.officesCache()) {
      return of(this.officesCache()!);
    }
    return this.http.get<Office[]>(`${this.apiUrl}/offices`).pipe(
      tap(offices => this.officesCache.set(offices))
    );
  }

  listRoles(): Observable<Role[]> {
    if (this.rolesCache()) {
      return of(this.rolesCache()!);
    }
    return this.http.get<Role[]>(`${this.apiUrl}/roles`).pipe(
      tap(roles => this.rolesCache.set(roles))
    );
  }

  listPermissions(): Observable<Permission[]> {
    if (this.permissionsCache()) {
      return of(this.permissionsCache()!);
    }
    return this.http.get<Permission[]>(`${this.apiUrl}/permissions`).pipe(
      tap(perms => this.permissionsCache.set(perms))
    );
  }

  getRolePermissions(roleId: number): Observable<Permission[]> {
    return this.http.get<Permission[]>(`${this.apiUrl}/roles/${roleId}/permissions`);
  }

  listUsers(page = 1, search = ''): Observable<PaginatedUsers> {
    const params: Record<string, string> = { page: String(page) };

    if (search) {
      params['search'] = search;
    }

    return this.http.get<PaginatedUsers>(`${this.apiUrl}/user`, { params });
  }

  listAdmins(): Observable<User[]> {
    return this.http.get<User[]>(`${this.apiUrl}/admins`);
  }

  getUser(id: number) {
    return this.http.get<User>(`${this.apiUrl}/user/${id}`);
  }

  createUser(payload: UserPayload) {
    return this.http.post<User>(`${this.apiUrl}/user`, payload).pipe(
      tap(() => {
        // We don't necessarily need to clear caches here as users don't affect offices/roles
      })
    );
  }

  updateUser(id: number, payload: UserPayload) {
    return this.http.patch<User>(`${this.apiUrl}/user/${id}`, payload);
  }

  deleteUser(id: number) {
    return this.http.delete<{ message: string }>(`${this.apiUrl}/user/${id}`);
  }

  backupDatabase(): Observable<Blob> {
    return this.http.post(`${this.apiUrl}/database/backup`, {}, { responseType: 'blob' });
  }

  restoreDatabase(file: File): Observable<any> {
    const formData = new FormData();
    formData.append('file', file);
    return this.http.post(`${this.apiUrl}/database/restore`, formData).pipe(
      tap(() => {
        // Clear all caches after restore
        this.officesCache.set(null);
        this.rolesCache.set(null);
        this.permissionsCache.set(null);
      })
    );
  }
}

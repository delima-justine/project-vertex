import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { User, Office, Role } from '../models/smis.model';

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
};

@Injectable({
  providedIn: 'root',
})
export class UserManagementService {
  private http = inject(HttpClient);
  private apiUrl = 'http://localhost:8000/api';

  listOffices(): Observable<Office[]> {
    return this.http.get<Office[]>(`${this.apiUrl}/offices`);
  }

  listRoles(): Observable<Role[]> {
    return this.http.get<Role[]>(`${this.apiUrl}/roles`);
  }

  listUsers(page = 1, search = ''): Observable<PaginatedUsers> {
    const params: Record<string, string> = { page: String(page) };

    if (search) {
      params['search'] = search;
    }

    return this.http.get<PaginatedUsers>(`${this.apiUrl}/user`, { params });
  }

  getUser(id: number) {
    return this.http.get<User>(`${this.apiUrl}/user/${id}`);
  }

  createUser(payload: UserPayload) {
    return this.http.post<User>(`${this.apiUrl}/user`, payload);
  }

  updateUser(id: number, payload: UserPayload) {
    return this.http.patch<User>(`${this.apiUrl}/user/${id}`, payload);
  }

  deleteUser(id: number) {
    return this.http.delete<{ message: string }>(`${this.apiUrl}/user/${id}`);
  }
}

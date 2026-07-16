import { HttpClient, HttpParams } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { AdminAudit, AuditFilters, PaginatedResponse } from '../models/smis.model';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root',
})
export class AuditService {
  private http = inject(HttpClient);
  private apiUrl = environment.apiUrl;

  /**
   * List admin audits with optional filters.
   */
  listAudits(filters: AuditFilters = {}): Observable<PaginatedResponse<AdminAudit>> {
    let params = new HttpParams();

    if (filters.page) params = params.set('page', filters.page.toString());
    if (filters.limit) params = params.set('limit', filters.limit.toString());
    if (filters.search) params = params.set('search', filters.search);
    if (filters.action_type) params = params.set('action_type', filters.action_type);
    if (filters.admin_id) params = params.set('admin_id', filters.admin_id.toString());
    if (filters.time_period) params = params.set('time_period', filters.time_period);
    if (filters.start_date) params = params.set('start_date', filters.start_date);
    if (filters.end_date) params = params.set('end_date', filters.end_date);

    return this.http.get<PaginatedResponse<AdminAudit>>(`${this.apiUrl}/admin-audits`, { params });
  }

  /**
   * Get a specific audit record.
   */
  getAudit(id: number): Observable<AdminAudit> {
    return this.http.get<AdminAudit>(`${this.apiUrl}/admin-audits/${id}`);
  }
}

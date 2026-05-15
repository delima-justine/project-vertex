import { HttpClient, HttpContext, HttpParams } from '@angular/common/http';
import { inject, Injectable, signal } from '@angular/core';
import { Observable, of, tap } from 'rxjs';
import { environment } from '../environments/environment';
import { Supply, Category, Unit, SupplyRequest, Archive, Office, PaginatedResponse } from '../models/smis.model';
import { NGX_LOADING_BAR_IGNORED } from '@ngx-loading-bar/http-client';

@Injectable({
  providedIn: 'root',
})
export class SupplyService {
  private http = inject(HttpClient);
  private apiUrl = environment.apiUrl;

  private categoriesCache = signal<Category[] | null>(null);
  private unitsCache = signal<Unit[] | null>(null);
  private officesCache = signal<Office[] | null>(null);

  statusCounts = signal<{ pending: number, approved: number, released: number, disapproved: number }>({
    pending: 0,
    approved: 0,
    released: 0,
    disapproved: 0
  });

  listSupplies(): Observable<Supply[]> {
    return this.http.get<Supply[]>(`${this.apiUrl}/supplies`);
  }

  listCategories(): Observable<Category[]> {
    if (this.categoriesCache()) {
      return of(this.categoriesCache()!);
    }
    return this.http.get<Category[]>(`${this.apiUrl}/categories`).pipe(
      tap(data => this.categoriesCache.set(data))
    );
  }

  createCategory(name: string): Observable<Category> {
    return this.http.post<Category>(`${this.apiUrl}/categories`, { category_name: name }).pipe(
      tap(() => this.categoriesCache.set(null))
    );
  }

  deleteCategory(id: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.apiUrl}/categories/${id}`).pipe(
      tap(() => this.categoriesCache.set(null))
    );
  }

  listUnits(): Observable<Unit[]> {
    if (this.unitsCache()) {
      return of(this.unitsCache()!);
    }
    return this.http.get<Unit[]>(`${this.apiUrl}/units`).pipe(
      tap(data => this.unitsCache.set(data))
    );
  }

  createUnit(name: string): Observable<Unit> {
    return this.http.post<Unit>(`${this.apiUrl}/units`, { unit_name: name }).pipe(
      tap(() => this.unitsCache.set(null))
    );
  }

  deleteUnit(id: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.apiUrl}/units/${id}`).pipe(
      tap(() => this.unitsCache.set(null))
    );
  }

  getSupply(stockNum: string): Observable<Supply> {
    return this.http.get<Supply>(`${this.apiUrl}/supplies/${stockNum}`);
  }

  createSupply(payload: Partial<Supply>): Observable<Supply> {
    return this.http.post<Supply>(`${this.apiUrl}/supplies`, payload);
  }

  updateSupply(stockNum: string, payload: Partial<Supply>): Observable<Supply> {
    return this.http.patch<Supply>(`${this.apiUrl}/supplies/${stockNum}`, payload);
  }

  deleteSupply(stockNum: string): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.apiUrl}/supplies/${stockNum}`);
  }

  getSupplyHistory(stockNum: string): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/supplies/${stockNum}/history`);
  }

  createSupplyRequest(payload: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/supply-requests`, payload).pipe(
      tap(() => this.getStatusCounts().subscribe())
    );
  }

  createBatchSupplyRequest(payload: { user_id: number, batch_id?: string, purpose?: string, items: { supply_id: string, quantity_req: number }[] }): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/supply-requests/batch-store`, payload).pipe(
      tap(() => this.getStatusCounts().subscribe())
    );
  }

  listSupplyRequests(status?: string, userId?: number, page?: number, perPage?: number): Observable<PaginatedResponse<SupplyRequest>> {
    let params = new HttpParams();
    if (status) {
      params = params.set('status', status);
    }
    if (userId) {
      params = params.set('user_id', userId.toString());
    }
    if (page) {
      params = params.set('page', page.toString());
    }
    if (perPage) {
      params = params.set('per_page', perPage.toString());
    }
    return this.http.get<PaginatedResponse<SupplyRequest>>(`${this.apiUrl}/supply-requests`, { params });
  }

  listOffices(): Observable<Office[]> {
    if (this.officesCache()) {
      return of(this.officesCache()!);
    }
    return this.http.get<Office[]>(`${this.apiUrl}/offices`).pipe(
      tap(data => this.officesCache.set(data))
    );
  }

  listArchives(): Observable<Archive[]> {
    return this.http.get<Archive[]>(`${this.apiUrl}/archives`);
  }

  createArchive(requestId: number): Observable<Archive> {
    return this.http.post<Archive>(`${this.apiUrl}/archives`, {
      request_id: requestId,
    });
  }

  restoreArchive(id: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/archives/${id}/restore`, {});
  }

  getSupplyRequest(id: number): Observable<SupplyRequest> {
    return this.http.get<SupplyRequest>(`${this.apiUrl}/supply-requests/${id}`);
  }

  updateSupplyRequest(id: number, payload: Partial<SupplyRequest>): Observable<SupplyRequest> {
    return this.http.patch<SupplyRequest>(`${this.apiUrl}/supply-requests/${id}`, payload).pipe(
      tap(() => this.getStatusCounts().subscribe())
    );
  }

  updateBatchSupplyRequest(payload: { items: { id: number, quantity_req?: number }[], status: string, approved_by?: number }): Observable<any> {
    return this.http.patch<any>(`${this.apiUrl}/supply-requests/batch-update`, payload).pipe(
      tap(() => this.getStatusCounts().subscribe())
    );
  }

  getStatusCounts(): Observable<{ pending: number, approved: number, released: number, disapproved: number }> {
    return this.http.get<{ pending: number, approved: number, released: number, disapproved: number }>(
      `${this.apiUrl}/supply-requests/status-counts`, 
      { context: new HttpContext().set(NGX_LOADING_BAR_IGNORED, true) }
    ).pipe(
      tap(counts => this.statusCounts.set(counts))
    );
  }
}

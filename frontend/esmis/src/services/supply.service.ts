import { HttpClient, HttpParams } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';
import { Supply, Category, Unit, SupplyRequest, Archive, Office, PaginatedResponse } from '../models/smis.model';

@Injectable({
  providedIn: 'root',
})
export class SupplyService {
  private http = inject(HttpClient);
  private apiUrl = environment.apiUrl;

  listSupplies(): Observable<Supply[]> {
    return this.http.get<Supply[]>(`${this.apiUrl}/supplies`);
  }

  listCategories(): Observable<Category[]> {
    return this.http.get<Category[]>(`${this.apiUrl}/categories`);
  }

  createCategory(name: string): Observable<Category> {
    return this.http.post<Category>(`${this.apiUrl}/categories`, { category_name: name });
  }

  deleteCategory(id: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.apiUrl}/categories/${id}`);
  }

  listUnits(): Observable<Unit[]> {
    return this.http.get<Unit[]>(`${this.apiUrl}/units`);
  }

  createUnit(name: string): Observable<Unit> {
    return this.http.post<Unit>(`${this.apiUrl}/units`, { unit_name: name });
  }

  deleteUnit(id: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${this.apiUrl}/units/${id}`);
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
    return this.http.post<any>(`${this.apiUrl}/supply-requests`, payload);
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
    return this.http.get<Office[]>(`${this.apiUrl}/offices`);
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
    return this.http.patch<SupplyRequest>(`${this.apiUrl}/supply-requests/${id}`, payload);
  }

  updateBatchSupplyRequest(payload: { ids: number[], status: string, approved_by?: number }): Observable<any> {
    return this.http.patch<any>(`${this.apiUrl}/supply-requests/batch-update`, payload);
  }
}

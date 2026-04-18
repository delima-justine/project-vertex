import { HttpClient, HttpParams } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { Supply, Category, Unit, SupplyRequest } from '../models/smis.model';

@Injectable({
  providedIn: 'root',
})
export class SupplyService {
  private http = inject(HttpClient);
  private apiUrl = 'http://localhost:8000/api';

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

  createSupplyRequest(payload: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/supply-requests`, payload);
  }

  listSupplyRequests(status?: string, userId?: number): Observable<SupplyRequest[]> {
    let params = new HttpParams();
    if (status) {
      params = params.set('status', status);
    }
    if (userId) {
      params = params.set('user_id', userId.toString());
    }
    return this.http.get<SupplyRequest[]>(`${this.apiUrl}/supply-requests`, { params });
  }

  getSupplyRequest(id: number): Observable<SupplyRequest> {
    return this.http.get<SupplyRequest>(`${this.apiUrl}/supply-requests/${id}`);
  }

  updateSupplyRequest(id: number, payload: Partial<SupplyRequest>): Observable<SupplyRequest> {
    return this.http.patch<SupplyRequest>(`${this.apiUrl}/supply-requests/${id}`, payload);
  }
}

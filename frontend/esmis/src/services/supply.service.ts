import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { Supply, Category, Unit } from '../models/smis.model';

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

  listUnits(): Observable<Unit[]> {
    return this.http.get<Unit[]>(`${this.apiUrl}/units`);
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
}

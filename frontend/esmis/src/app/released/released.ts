import { Component, inject, OnInit, signal, computed } from '@angular/core';
import { Sidebar } from "../sidebar/sidebar";
import { SupplyService } from '../../services/supply.service';
import { SupplyRequest } from '../../models/smis.model';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-released',
  standalone: true,
  imports: [Sidebar, CommonModule, FormsModule],
  templateUrl: './released.html',
  styleUrl: './released.scss',
})
export class Released implements OnInit {
  private supplyService = inject(SupplyService);

  requests = signal<SupplyRequest[]>([]);
  searchTerm = signal('');

  filteredRequests = computed(() => {
    return this.requests().filter(req => {
      const search = this.searchTerm().toLowerCase();
      return !search || 
             req.supply?.item_desc.toLowerCase().includes(search) ||
             req.supply_id.toLowerCase().includes(search) ||
             req.user?.first_name.toLowerCase().includes(search) ||
             req.user?.last_name.toLowerCase().includes(search);
    });
  });

  ngOnInit() {
    this.loadReleasedRequests();
  }

  loadReleasedRequests() {
    this.supplyService.listSupplyRequests('released').subscribe({
      next: (data) => this.requests.set(data),
      error: (err) => console.error('Error fetching released requests', err)
    });
  }
}

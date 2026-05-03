import { Component, inject, OnInit, signal, computed } from '@angular/core';
import { Sidebar } from "../sidebar/sidebar";
import { SupplyService } from '../../services/supply.service';
import { AuthService } from '../../services/auth.service';
import { SupplyRequest } from '../../models/smis.model';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { TopNav } from "../top-nav/top-nav";

@Component({
  selector: 'app-disapproved',
  standalone: true,
  imports: [Sidebar, CommonModule, FormsModule, TopNav],
  templateUrl: './disapproved.html',
  styleUrl: './disapproved.scss',
})
export class Disapproved implements OnInit {
  private supplyService = inject(SupplyService);
  private authService = inject(AuthService);

  user = this.authService.currentUser;

  requests = signal<SupplyRequest[]>([]);
  searchTerm = signal('');
  selectedBatch = signal<SupplyRequest[]>([]);
  activeTabIndex = signal(0);

  batchedRequests = computed(() => {
    const groups: { [key: string]: SupplyRequest[] } = {};
    
    this.requests().forEach(req => {
      // Use local date string (YYYY-MM-DD) for batching
      const dateObj = new Date(req.created_at);
      const date = `${dateObj.getFullYear()}-${String(dateObj.getMonth() + 1).padStart(2, '0')}-${String(dateObj.getDate()).padStart(2, '0')}`;
      const key = `${req.user_id}_${date}`;
      
      if (!groups[key]) {
        groups[key] = [];
      }
      groups[key].push(req);
    });

    return Object.values(groups).map(batch => ({
      id: batch[0].id,
      user: batch[0].user,
      office: batch[0].user?.office?.office_name,
      date: new Date(batch[0].created_at),
      requests: batch,
      itemSummary: batch.length > 1 ? `${batch[0].supply?.item_desc} and ${batch.length - 1} more...` : batch[0].supply?.item_desc,
      totalQty: batch.reduce((sum, r) => sum + r.quantity_req, 0)
    }));
  });

  filteredRequests = computed(() => {
    return this.batchedRequests().filter(batch => {
      const search = this.searchTerm().toLowerCase();
      return !search || 
             batch.user?.first_name.toLowerCase().includes(search) ||
             batch.user?.last_name.toLowerCase().includes(search) ||
             batch.requests.some(r => 
               r.supply?.item_desc.toLowerCase().includes(search) || 
               r.supply_id.toLowerCase().includes(search)
             );
    });
  });

  ngOnInit() {
    this.loadDisapprovedRequests();
  }

  loadDisapprovedRequests() {
    this.supplyService.listSupplyRequests('disapproved').subscribe({
      next: (data) => this.requests.set(data),
      error: (err) => console.error('Error fetching disapproved requests', err)
    });
  }

  viewBatch(batch: SupplyRequest[]) {
    this.selectedBatch.set(batch);
    this.activeTabIndex.set(0);
    const modalElement = document.getElementById('requestDetailsModal');
    if (modalElement) {
      const modal = (window as any).bootstrap.Modal.getOrCreateInstance(modalElement);
      modal.show();
    }
  }
}

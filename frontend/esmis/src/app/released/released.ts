import { Component, inject, OnInit, signal, computed, ViewChild, ElementRef } from '@angular/core';
import { SupplyService } from '../../services/supply.service';
import { AuthService } from '../../services/auth.service';
import { SupplyRequest } from '../../models/smis.model';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-released',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './released.html',
  styleUrl: './released.scss',
})
export class Released implements OnInit {
  private supplyService = inject(SupplyService);
  private authService = inject(AuthService);

  @ViewChild('requestDetailsModal') modalElement?: ElementRef;

  user = this.authService.currentUser;

  requests = signal<SupplyRequest[]>([]);
  currentPage = signal(1);
  lastPage = signal(1);
  isLoading = signal(false);
  feedback = signal('');
  searchTerm = signal('');
  selectedOffice = signal('all');
  selectedBatch = signal<SupplyRequest[]>([]);
  activeTabIndex = signal(0);

  batchedRequests = computed(() => {
    const groups: { [key: string]: SupplyRequest[] } = {};
    
    this.requests().forEach(req => {
      // Use batch_id if available, otherwise fallback to user_id + local date string
      let key = req.batch_id;
      
      if (!key) {
        const dateObj = new Date(req.created_at);
        const date = `${dateObj.getFullYear()}-${String(dateObj.getMonth() + 1).padStart(2, '0')}-${String(dateObj.getDate()).padStart(2, '0')}`;
        key = `LEGACY_${req.user_id}_${date}`;
      }
      
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
      const matchesSearch = !search || 
             batch.user?.first_name.toLowerCase().includes(search) ||
             batch.user?.last_name.toLowerCase().includes(search) ||
             batch.requests.some(r => 
               r.supply?.item_desc.toLowerCase().includes(search) || 
               r.supply_id.toLowerCase().includes(search)
             );

      const matchesOffice = this.selectedOffice() === 'all' || batch.office === this.selectedOffice();

      return matchesSearch && matchesOffice;
    });
  });

  offices = computed(() => {
    const allOffices = this.requests().map(r => r.user?.office?.office_name).filter(Boolean) as string[];
    return [...new Set(allOffices)];
  });

  ngOnInit() {
    this.loadReleasedRequests();
  }

  loadReleasedRequests(page: number = 1) {
    this.isLoading.set(true);
    this.supplyService.listSupplyRequests('released', undefined, page).subscribe({
      next: (response) => {
        this.requests.set(response.data);
        this.currentPage.set(response.current_page);
        this.lastPage.set(response.last_page);
        this.isLoading.set(false);
      },
      error: (err) => {
        console.error('Error fetching released requests', err);
        this.isLoading.set(false);
      }
    });
  }

  changePage(page: number) {
    if (page >= 1 && page <= this.lastPage()) {
      this.loadReleasedRequests(page);
    }
  }

  viewBatch(batch: SupplyRequest[]) {
    this.selectedBatch.set(batch);
    this.activeTabIndex.set(0);
    if (this.modalElement) {
      const modal = (window as any).bootstrap.Modal.getOrCreateInstance(this.modalElement.nativeElement);
      modal.show();
    }
  }
}

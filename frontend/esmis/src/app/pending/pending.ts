import { Component, inject, OnInit, signal, computed } from '@angular/core';
import { Sidebar } from "../sidebar/sidebar";
import { SupplyService } from '../../services/supply.service';
import { AuthService } from '../../services/auth.service';
import { SupplyRequest } from '../../models/smis.model';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';

@Component({
  selector: 'app-pending',
  standalone: true,
  imports: [Sidebar, CommonModule, FormsModule],
  templateUrl: './pending.html',
  styleUrl: './pending.scss',
})
export class Pending implements OnInit {
  private supplyService = inject(SupplyService);
  private authService = inject(AuthService);

  requests = signal<SupplyRequest[]>([]);
  searchTerm = signal('');
  selectedOffice = signal('all');

  selectedBatch = signal<SupplyRequest[]>([]);
  isModalOpen = signal(false);

  router = inject(Router);

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
      const matchesSearch = !search || 
                            batch.user?.first_name.toLowerCase().includes(search) ||
                            batch.user?.last_name.toLowerCase().includes(search) ||
                            batch.requests.some(r => r.supply?.item_desc.toLowerCase().includes(search) || r.supply_id.toLowerCase().includes(search));

      const matchesOffice = this.selectedOffice() === 'all' || batch.office === this.selectedOffice();

      return matchesSearch && matchesOffice;
    });
  });

  offices = computed(() => {
    const allOffices = this.requests().map(r => r.user?.office?.office_name).filter(Boolean) as string[];
    return [...new Set(allOffices)];
  });

  ngOnInit() {
    this.loadPendingRequests();
  }

  loadPendingRequests() {
    this.supplyService.listSupplyRequests('pending').subscribe({
      next: (data) => this.requests.set(data),
      error: (err) => console.error('Error fetching pending requests', err)
    });
  }

  disapproveRequest(request: SupplyRequest) {
    if (confirm(`Are you sure you want to disapprove request for ${request.supply?.item_desc}?`)) {
      this.supplyService.updateSupplyRequest(request.id, {
        status: 'disapproved'
      }).subscribe({
        next: () => {
          alert('Request disapproved.');
          this.loadPendingRequests();
          const remaining = this.selectedBatch().filter(r => r.id !== request.id);
          if (remaining.length === 0) {
            this.closeModal();
          } else {
            this.selectedBatch.set(remaining);
          }
        },
        error: (err) => {
          console.error('Error disapproving request', err);
          alert('Failed to disapprove request.');
        }
      });
    }
  }

  disapproveBatch() {
    if (confirm(`Are you sure you want to disapprove all ${this.selectedBatch().length} requests in this batch?`)) {
      const updates = this.selectedBatch().map(req => 
        this.supplyService.updateSupplyRequest(req.id, { status: 'disapproved' })
      );

      import('rxjs').then(({ forkJoin }) => {
        forkJoin(updates).subscribe({
          next: () => {
            alert('All requests in batch disapproved.');
            this.loadPendingRequests();
            this.closeModal();
          },
          error: (err) => {
            console.error('Error disapproving batch', err);
            alert('Failed to disapprove some requests.');
          }
        });
      });
    }
  }

  editBatchRIS() {
    const ids = this.selectedBatch().map(r => r.id).join(',');
    this.router.navigate(['/requests/edit-ris', ids]);
  }

  viewRequest(batch: any) {
    this.selectedBatch.set(batch.requests);
    this.isModalOpen.set(true);
  }

  closeModal() {
    this.isModalOpen.set(false);
    this.selectedBatch.set([]);
  }
}

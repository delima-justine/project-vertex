import { Component, inject, OnInit, signal, computed, ViewChild, ElementRef } from '@angular/core';
import { Sidebar } from "../sidebar/sidebar";
import { SupplyService } from '../../services/supply.service';
import { AuthService } from '../../services/auth.service';
import { ToastService } from '../../services/toast.service';
import { ConfirmService } from '../../services/confirm.service';
import { SupplyRequest } from '../../models/smis.model';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { TopNav } from "../top-nav/top-nav";

@Component({
  selector: 'app-pending',
  standalone: true,
  imports: [Sidebar, CommonModule, FormsModule, TopNav],
  templateUrl: './pending.html',
  styleUrl: './pending.scss',
})
export class Pending implements OnInit {
  supplyService = inject(SupplyService);
  authService = inject(AuthService);
  toastService = inject(ToastService);
  confirmService = inject(ConfirmService);

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

  @ViewChild('batchModal', { static: false }) batchModalElement?: ElementRef<HTMLElement>;

  router = inject(Router);

  private getModalInstance() {
    if (!this.batchModalElement) return null;
    const bootstrap = (window as any).bootstrap;
    if (bootstrap) {
      return bootstrap.Modal.getOrCreateInstance(this.batchModalElement.nativeElement);
    }
    return null;
  }

  openModal() {
    this.getModalInstance()?.show();
  }

  closeModal() {
    this.getModalInstance()?.hide();
  }

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

  loadPendingRequests(page: number = 1) {
    this.isLoading.set(true);
    this.supplyService.listSupplyRequests('pending', undefined, page).subscribe({
      next: (response) => {
        this.requests.set(response.data);
        this.currentPage.set(response.current_page);
        this.lastPage.set(response.last_page);
        this.isLoading.set(false);
      },
      error: (err) => {
        console.error('Error fetching pending requests', err);
        this.isLoading.set(false);
      }
    });
  }

  changePage(page: number) {
    if (page >= 1 && page <= this.lastPage()) {
      this.loadPendingRequests(page);
    }
  }

  viewRequest(batch: any) {
    this.selectedBatch.set(batch.requests);
    this.activeTabIndex.set(0);
    this.openModal();
  }

  async disapproveBatch() {
    const confirmed = await this.confirmService.confirm('Are you sure you want to disapprove this entire batch?', {
      title: 'Disapprove Batch',
      confirmText: 'Disapprove',
      danger: true
    });

    if (confirmed) {
      const batch = this.selectedBatch();
      const adminId = this.user()?.id;
      const requests = batch.map(r => this.supplyService.updateSupplyRequest(r.id, { 
        status: 'disapproved',
        approved_by: adminId
      }));
      
      // Simple loop subscribe for now
      let completed = 0;
      requests.forEach(obs => {
        obs.subscribe({
          next: () => {
            completed++;
            if (completed === batch.length) {
              this.closeModal();
              this.loadPendingRequests();
              this.toastService.success('Batch disapproved.');
            }
          }
        });
      });
    }
  }

  editBatchRIS() {
    const batch = this.selectedBatch();
    if (batch.length > 0) {
      const ids = batch.map(r => r.id).join(',');
      this.closeModal();
      this.router.navigate(['/requests/edit-ris', ids]);
    }
  }
}

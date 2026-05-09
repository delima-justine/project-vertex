import { Component, inject, OnInit, signal, computed, ViewChild, ElementRef, ChangeDetectorRef } from '@angular/core';
import { Sidebar } from "../sidebar/sidebar";
import { SupplyService } from '../../services/supply.service';
import { AuthService } from '../../services/auth.service';
import { SupplyRequest } from '../../models/smis.model';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { TopNav } from "../top-nav/top-nav";
import { forkJoin } from 'rxjs';

@Component({
  selector: 'app-approved',
  standalone: true,
  imports: [Sidebar, CommonModule, FormsModule, TopNav],
  templateUrl: './approved.html',
  styleUrl: './approved.scss',
})
export class Approved implements OnInit {
  private supplyService = inject(SupplyService);
  private authService = inject(AuthService);
  private cdr = inject(ChangeDetectorRef);

  @ViewChild('requestDetailsModal') modalElement?: ElementRef;

  user = this.authService.currentUser;

  requests = signal<SupplyRequest[]>([]);
  searchTerm = signal('');
  selectedOffice = signal('all');
  selectedBatch = signal<SupplyRequest[]>([]);
  activeTabIndex = signal(0);

  notifMessage = signal('');
  notifType = signal<'success' | 'error' | 'warning'>('success');
  notifTitle = signal('');
  notifIcon = computed(() => {
    switch (this.notifType()) {
      case 'success': return 'bi-check-circle-fill';
      case 'error': return 'bi-exclamation-triangle-fill';
      case 'warning': return 'bi-exclamation-triangle-fill';
      default: return 'bi-info-circle-fill';
    }
  });

  confirmMessage = signal('');
  confirmAction = signal<() => void>(() => {});

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
    this.loadApprovedRequests();
  }

  loadApprovedRequests() {
    this.supplyService.listSupplyRequests('approved').subscribe({
      next: (data) => this.requests.set(data),
      error: (err) => console.error('Error fetching approved requests', err)
    });
  }

  showNotification(message: string, type: 'success' | 'error' | 'warning') {
    this.notifMessage.set(message);
    this.notifType.set(type);
    this.notifTitle.set(type === 'success' ? 'Success' : type === 'error' ? 'Warning' : 'Error');
    this.cdr.detectChanges();
    const modalElement = document.getElementById('notifModal-approved');
    if (modalElement) {
      setTimeout(() => {
        const modal = (window as any).bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
      }, 0);
    }
  }

  openConfirmModal(message: string, action: () => void) {
    this.confirmMessage.set(message);
    this.confirmAction.set(action);
    const modalElement = document.getElementById('confirmModal-approved');
    if (modalElement) {
      const modal = (window as any).bootstrap.Modal.getOrCreateInstance(modalElement);
      modal.show();
    }
  }

  closeConfirmModal() {
    const modalElement = document.getElementById('confirmModal-approved');
    if (modalElement) {
      const modal = (window as any).bootstrap.Modal.getOrCreateInstance(modalElement);
      modal.hide();
    }
  }

  runConfirmAction() {
    this.confirmAction()();
    this.closeConfirmModal();
  }

  viewBatch(batch: SupplyRequest[]) {
    this.selectedBatch.set(batch);
    this.activeTabIndex.set(0);
    if (this.modalElement) {
      const modal = (window as any).bootstrap.Modal.getOrCreateInstance(this.modalElement.nativeElement);
      modal.show();
    }
  }

  releaseBatch(batch: SupplyRequest[]) {
    const itemNames = batch.map(r => r.supply?.item_desc).join(', ');
    this.openConfirmModal(`Are you sure you want to release the following items: ${itemNames}?`, () => {
      const observables = batch.map(req => 
        this.supplyService.updateSupplyRequest(req.id, { status: 'released' })
      );

      forkJoin(observables).subscribe({
        next: () => {
          this.showNotification('Batch requests released!', 'success');
          this.loadApprovedRequests();
        },
        error: (err) => {
          console.error('Error releasing request batch', err);
          this.showNotification('Failed to release some requests in the batch.', 'error');
          this.loadApprovedRequests();
        }
      });
    });
  }
}

import { Component, computed, inject, OnInit, signal, effect } from '@angular/core';
import { SupplyService } from '../../services/supply.service';
import { AuthService } from '../../services/auth.service';
import { ToastService } from '../../services/toast.service';
import { Supply, Category } from '../../models/smis.model';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { forkJoin } from 'rxjs';

@Component({
  selector: 'app-create-request',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './create-request.html',
  styleUrl: './create-request.scss',
})
export class CreateRequest implements OnInit {
  private supplyService = inject(SupplyService);
  private authService = inject(AuthService);
  private toastService = inject(ToastService);

  protected readonly Math = Math;

  availableSupplies = signal<Supply[]>([]);
  categories = signal<Category[]>([]);
  requestList: (Supply & { quantity_req: number })[] = [];
  
  // Temporary selection list for the modal
  tempSelectedItems: (Supply & { quantity_req: number })[] = [];
  
  // Filters for modal
  modalSearchTerm = signal('');
  modalCategoryFilter = signal('all');
  modalStatusFilter = signal('all');

  // Pagination for modal
  currentPage = signal(1);
  itemsPerPage = signal(10);

  filteredAvailableSupplies = computed(() => {
    const supplies = this.availableSupplies();
    const term = this.modalSearchTerm().toLowerCase();
    const category = this.modalCategoryFilter();
    const status = this.modalStatusFilter();

    return supplies.filter(s => {
      const matchesSearch = !term || 
        s.item_desc.toLowerCase().includes(term) || 
        s.stock_num.toLowerCase().includes(term);
      
      const matchesCategory = category === 'all' || s.category_id.toString() === category;
      const matchesStatus = status === 'all' || s.status === status;

      return matchesSearch && matchesCategory && matchesStatus;
    });
  });

  paginatedSupplies = computed(() => {
    const supplies = this.filteredAvailableSupplies();
    const start = (this.currentPage() - 1) * this.itemsPerPage();
    const end = start + this.itemsPerPage();
    return supplies.slice(start, end);
  });

  totalPages = computed(() => {
    return Math.ceil(this.filteredAvailableSupplies().length / this.itemsPerPage());
  });
  
  purpose = '';
  purposeError = signal(false);

  constructor() {
    // Reset page to 1 when filters change
    effect(() => {
      this.modalSearchTerm();
      this.modalCategoryFilter();
      this.modalStatusFilter();
      this.currentPage.set(1);
    }, { allowSignalWrites: true });
  }

  ngOnInit() {
    this.loadData();
  }

  loadData() {
    this.supplyService.listSupplies().subscribe({
      next: (supplies) => {
        this.availableSupplies.set(supplies);
      },
      error: (err) => console.error('Error fetching supplies', err)
    });

    this.supplyService.listCategories().subscribe({
      next: (cats) => this.categories.set(cats),
      error: (err) => console.error('Error fetching categories', err)
    });
  }

  openModal() {
    // Reset filters
    this.modalSearchTerm.set('');
    this.modalCategoryFilter.set('all');
    this.modalStatusFilter.set('all');
    this.currentPage.set(1);
    
    // Clone current request list into temporary list
    this.tempSelectedItems = JSON.parse(JSON.stringify(this.requestList));
    
    const modalElement = document.getElementById('supplyInventoryModal');
    if (modalElement) {
      const modal = (window as any).bootstrap.Modal.getOrCreateInstance(modalElement);
      modal.show();
    }
  }

  closeModal() {
    const modalElement = document.getElementById('supplyInventoryModal');
    if (modalElement) {
      const modal = (window as any).bootstrap.Modal.getOrCreateInstance(modalElement);
      modal.hide();
    }
  }

  confirmSelection() {
    // Save temporary selection to main list
    this.requestList = JSON.parse(JSON.stringify(this.tempSelectedItems));
    this.closeModal();
  }

  isItemSelected(stockNum: string): boolean {
    return this.tempSelectedItems.some(s => s.stock_num === stockNum);
  }

  toggleItem(supply: Supply) {
    const index = this.tempSelectedItems.findIndex(s => s.stock_num === supply.stock_num);
    if (index > -1) {
      this.tempSelectedItems.splice(index, 1);
    } else if (supply.status !== 'Out of Stock') {
      this.tempSelectedItems.push({ ...supply, quantity_req: 1 });
    }
  }

  areAllItemsSelected(): boolean {
    const available = this.filteredAvailableSupplies().filter(s => s.status !== 'Out of Stock');
    if (available.length === 0) return false;
    return available.every(s => this.isItemSelected(s.stock_num));
  }

  toggleAllItems() {
    if (this.areAllItemsSelected()) {
      // Remove all items that are in filtered results from tempSelectedItems
      const filteredStockNums = new Set(this.filteredAvailableSupplies().map(s => s.stock_num));
      this.tempSelectedItems = this.tempSelectedItems.filter(s => !filteredStockNums.has(s.stock_num));
    } else {
      this.filteredAvailableSupplies().forEach(supply => {
        if (supply.status !== 'Out of Stock' && !this.isItemSelected(supply.stock_num)) {
          this.tempSelectedItems.push({ ...supply, quantity_req: 1 });
        }
      });
    }
  }

  goToPage(page: number) {
    if (page >= 1 && page <= this.totalPages()) {
      this.currentPage.set(page);
    }
  }

  nextPage() {
    if (this.currentPage() < this.totalPages()) {
      this.currentPage.set(this.currentPage() + 1);
    }
  }

  prevPage() {
    if (this.currentPage() > 1) {
      this.currentPage.set(this.currentPage() - 1);
    }
  }

  getPaginationRange(): number[] {
    const total = this.totalPages();
    const current = this.currentPage();
    const range: number[] = [];
    const maxVisible = 5;

    let start = Math.max(1, current - Math.floor(maxVisible / 2));
    let end = Math.min(total, start + maxVisible - 1);

    if (end - start + 1 < maxVisible) {
      start = Math.max(1, end - maxVisible + 1);
    }

    for (let i = start; i <= end; i++) {
      range.push(i);
    }
    return range;
  }

  removeItem(index: number) {
    this.requestList.splice(index, 1);
  }

  onPurposeInput() {
    if (this.purposeError()) {
      this.purposeError.set(false);
    }
  }

  submitRequest() {
    if (!this.purpose || !this.purpose.trim()) {
      this.purposeError.set(true);
      return;
    }

    const user = this.authService.currentUser();
    if (!user) {
      this.toastService.error('You must be logged in to submit a request.');
      return;
    }

    const batchId = `BATCH-${user.id}-${Date.now()}`;

    const payload = {
      user_id: user.id,
      batch_id: batchId,
      purpose: this.purpose,
      items: this.requestList.map(item => ({
        supply_id: item.stock_num,
        quantity_req: item.quantity_req
      }))
    };

    this.supplyService.createBatchSupplyRequest(payload).subscribe({
      next: (response) => {
        this.toastService.success('Request submitted successfully!');
        this.requestList = [];
        this.purpose = '';
      },
      error: (err) => {
        console.error('Error submitting requests', err);
        this.toastService.error('There was an error submitting your request. Please check the console.');
      }
    });
  }
}

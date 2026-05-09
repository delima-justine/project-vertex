import { Component, computed, inject, OnInit, signal } from '@angular/core';
import { Sidebar } from "../sidebar/sidebar";
import { SupplyService } from '../../services/supply.service';
import { AuthService } from '../../services/auth.service';
import { ToastService } from '../../services/toast.service';
import { Supply, Category } from '../../models/smis.model';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { forkJoin } from 'rxjs';
import { TopNav } from "../top-nav/top-nav";

@Component({
  selector: 'app-create-request',
  standalone: true,
  imports: [Sidebar, CommonModule, FormsModule, TopNav],
  templateUrl: './create-request.html',
  styleUrl: './create-request.scss',
})
export class CreateRequest implements OnInit {
  private supplyService = inject(SupplyService);
  private authService = inject(AuthService);
  private toastService = inject(ToastService);

  availableSupplies = signal<Supply[]>([]);
  categories = signal<Category[]>([]);
  requestList: (Supply & { quantity_req: number })[] = [];
  
  // Temporary selection list for the modal
  tempSelectedItems: (Supply & { quantity_req: number })[] = [];
  
  // Filters for modal
  modalSearchTerm = signal('');
  modalCategoryFilter = signal('all');
  modalStatusFilter = signal('all');

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
  
  purpose = '';
  purposeError = signal(false);

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

    const requests = this.requestList.map(item => {
      return this.supplyService.createSupplyRequest({
        user_id: user.id,
        batch_id: batchId,
        supply_id: item.stock_num,
        quantity_req: item.quantity_req,
        purpose: this.purpose
      });
    });

    forkJoin(requests).subscribe({
      next: (responses) => {
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

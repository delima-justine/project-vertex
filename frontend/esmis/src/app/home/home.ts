import { Component, computed, inject, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { Sidebar } from "../sidebar/sidebar";
import { AuthService } from '../../services/auth.service';
import { SupplyService } from '../../services/supply.service';
import { Supply, Category, Unit, SupplyRequest } from '../../models/smis.model';
import { TopNav } from "../top-nav/top-nav";
import { BaseChartDirective } from 'ng2-charts';
import { ChartConfiguration, ChartData } from 'chart.js';

@Component({
  selector: 'app-home',
  imports: [
      Sidebar, 
      CommonModule, 
      ReactiveFormsModule, 
      FormsModule, 
      TopNav,
      BaseChartDirective
    ],
  templateUrl: './home.html',
  styleUrl: './home.scss',
})
export class Home implements OnInit {
  authService = inject(AuthService);
  supplyService = inject(SupplyService);
  fb = inject(FormBuilder);

  user = this.authService.currentUser;
  supplies = signal<Supply[]>([]);
  categories = signal<Category[]>([]);
  units = signal<Unit[]>([]);
  requests = signal<SupplyRequest[]>([]);

  // Filter signals
  searchTerm = signal('');
  selectedStatus = signal('all');
  selectedCategory = signal('all');
  newCategoryName = signal('');
  newUnitName = signal('');

  // Total Active Items
  totalActiveItems = computed(() => this.supplies().length);

  // Sum of all quantities across all items
  totalQuantity = computed(() => {
    return this.supplies().reduce((sum, item) => sum + (item.quantity || 0), 0);
  });

  // Count of items that are low stock
  needsRestockingCount = computed(() => {
    return this.supplies().filter(item => 
      item.status === 'Low Stock' || item.status === 'Out of Stock').length;
  });

  // Analytics Computation
  stockAvailabilityPercentage = computed(() => {
    const total = this.supplies().length;
    if (total === 0) return 0;
    const available = this.supplies().filter(s => s.status === 'Available').length;
    return Math.round((available / total) * 100);
  });

  restockingNeedPercentage = computed(() => {
    const total = this.supplies().length;
    if (total === 0) return 0;
    const low = this.supplies().filter(s => s.status === 'Low Stock' || s.status === 'Out of Stock').length;
    return Math.round((low / total) * 100);
  });

  recentlyReleasedPercentage = computed(() => {
    const total = this.requests().length;
    if (total === 0) return 0;
    const released = this.requests().filter(r => r.status === 'released').length;
    return Math.round((released / total) * 100);
  });

  public barChartData: ChartData<'bar'> = {
    labels: ['Good Stock', 'Low Stock', 'Out of Stock'],
    datasets: [
      {
        data: [12, 5, 2],
        label: 'Inventory Status',
        backgroundColor: ['#2ECC71', '#F1C40F', '#E74C3C'],
        borderColor: ['#0f5132', '#997404', '#842029'],
        borderWidth: 1,
        borderRadius: 8,
      }
    ]
  };

  public barChartOptions: ChartConfiguration['options'] = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {  display: false }
    }
  };

  filteredSupplies = computed(() => {
    return this.supplies().filter(supply => {
      const search = this.searchTerm().toLowerCase();
      const matchesSearch = !search || 
                            supply.item_desc.toLowerCase().includes(search) ||
                            supply.stock_num.toLowerCase().includes(search);

      const matchesStatus = this.selectedStatus() === 'all' || supply.status === this.selectedStatus();
      const matchesCategory = this.selectedCategory() === 'all' || supply.category_id.toString() === this.selectedCategory();

      return matchesSearch && matchesStatus && matchesCategory;
    });
  });

  supplyForm: FormGroup;
  isEditMode = false;
  currentStockNum: string | null = null;
  
  constructor() {
    this.supplyForm = this.fb.group({
      stock_num: ['', Validators.required],
      item_desc: ['', Validators.required],
      quantity: [0, [Validators.required, Validators.min(0)]],
      category_id: ['', Validators.required],
      unit_id: ['', Validators.required],
      status: ['Available'],
      remarks: [''],
    });
  }

  ngOnInit() {
    this.loadData();
  }

  loadData() {
    this.supplyService.listSupplies().subscribe((data) => this.supplies.set(data));
    this.supplyService.listCategories().subscribe((data) => this.categories.set(data));
    this.supplyService.listUnits().subscribe((data) => this.units.set(data));
    this.supplyService.listSupplyRequests().subscribe((data) => this.requests.set(data));
  }

  openAddModal() {
    this.isEditMode = false;
    this.currentStockNum = null;
    this.supplyForm.reset({
      quantity: 0,
      status: 'Available'
    });
    this.supplyForm.get('stock_num')?.enable();
    const modalElement = document.getElementById('supplyModal');
    if (modalElement) {
      const modal = (window as any).bootstrap.Modal.getOrCreateInstance(modalElement);
      modal.show();
    }
  }

  openEditModal(supply: Supply) {
    this.isEditMode = true;
    this.currentStockNum = supply.stock_num;
    this.supplyForm.patchValue(supply);
    this.supplyForm.get('stock_num')?.disable();
    const modalElement = document.getElementById('supplyModal');
    if (modalElement) {
      const modal = (window as any).bootstrap.Modal.getOrCreateInstance(modalElement);
      modal.show();
    }
  }

  onSubmit() {
    if (this.supplyForm.invalid) {
      this.supplyForm.markAllAsTouched();
      return;
    }

    const payload = this.supplyForm.getRawValue();
    if (this.isEditMode && this.currentStockNum) {
      this.supplyService.updateSupply(this.currentStockNum, payload).subscribe({
        next: () => {
          this.loadData();
          this.closeModal();
        },
        error: (err) => {
          console.error('Error updating supply:', err);
          alert('Failed to update supply. Please check the console for details.');
        }
      });
    } else {
      this.supplyService.createSupply(payload).subscribe({
        next: () => {
          this.loadData();
          this.closeModal();
        },
        error: (err) => {
          console.error('Error creating supply:', err);
          alert('Failed to create supply. Stock number might already exist.');
        }
      });
    }
  }

  onDelete(stockNum: string) {
    if (confirm('Are you sure you want to delete this supply?')) {
      this.supplyService.deleteSupply(stockNum).subscribe({
        next: () => {
          this.loadData();
        },
        error: (err) => {
          console.error('Error deleting supply:', err);
          alert('Failed to delete supply.');
        }
      });
    }
  }

  closeModal() {
    const modalElement = document.getElementById('supplyModal');
    if (modalElement) {
      const modal = (window as any).bootstrap.Modal.getOrCreateInstance(modalElement);
      modal.hide();
    }
  }

  // Category Management
  openCategoryModal() {
    this.closeModal();
    this.newCategoryName.set('');
    const modalElement = document.getElementById('categoryModal');
    if (modalElement) {
      const modal = (window as any).bootstrap.Modal.getOrCreateInstance(modalElement);
      modal.show();
    }
  }

  addCategory() {
    const name = this.newCategoryName().trim();
    if (!name) return;

    this.supplyService.createCategory(name).subscribe({
      next: () => {
        this.newCategoryName.set('');
        this.loadData();
      },
      error: (err) => {
        console.error('Error adding category:', err);
        alert('Failed to add category. It might already exist.');
      }
    });
  }

  deleteCategory(id: number) {
    if (confirm('Are you sure you want to delete this category? This might fail if supplies are using it.')) {
      this.supplyService.deleteCategory(id).subscribe({
        next: () => {
          this.loadData();
        },
        error: (err) => {
          console.error('Error deleting category:', err);
          alert('Failed to delete category. Ensure no supplies are linked to it.');
        }
      });
    }
  }

  closeCategoryModal() {
    const modalElement = document.getElementById('categoryModal');
    if (modalElement) {
      const modal = (window as any).bootstrap.Modal.getOrCreateInstance(modalElement);
      modal.hide();
    }
  }

  // Unit Management
  openUnitModal() {
    this.closeModal();
    this.newUnitName.set('');
    const modalElement = document.getElementById('unitModal');
    if (modalElement) {
      const modal = (window as any).bootstrap.Modal.getOrCreateInstance(modalElement);
      modal.show();
    }
  }

  addUnit() {
    const name = this.newUnitName().trim();
    if (!name) return;

    this.supplyService.createUnit(name).subscribe({
      next: () => {
        this.newUnitName.set('');
        this.loadData();
      },
      error: (err) => {
        console.error('Error adding unit:', err);
        alert('Failed to add unit. It might already exist.');
      }
    });
  }

  deleteUnit(id: number) {
    if (confirm('Are you sure you want to delete this unit? This might fail if supplies are using it.')) {
      this.supplyService.deleteUnit(id).subscribe({
        next: () => {
          this.loadData();
        },
        error: (err) => {
          console.error('Error deleting unit:', err);
          alert('Failed to delete unit. Ensure no supplies are linked to it.');
        }
      });
    }
  }

  closeUnitModal() {
    const modalElement = document.getElementById('unitModal');
    if (modalElement) {
      const modal = (window as any).bootstrap.Modal.getOrCreateInstance(modalElement);
      modal.hide();
    }
  }
}

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
  currentDate = signal(new Date());

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

  barChartData = computed<ChartData<'bar'>>(() => {
    const supplies = this.supplies();
    const goodStock = supplies.filter(s => s.status === 'Available').length;
    const lowStock = supplies.filter(s => s.status === 'Low Stock').length;
    const outOfStock = supplies.filter(s => s.status === 'Out of Stock').length;

    return {
      labels: ['Good Stock', 'Low Stock', 'Out of Stock'],
      datasets: [
        {
          data: [goodStock, lowStock, outOfStock],
          label: 'Inventory Status',
          backgroundColor: ['#2ECC71', '#F1C40F', '#E74C3C'],
          borderColor: ['#0f5132', '#997404', '#842029'],
          borderWidth: 1,
          borderRadius: 8,
        }
      ]
    };
  });

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

  // Report Generation
  printInventoryReport() {
    const supplies = this.filteredSupplies();
    const user = this.user();
    const dateStr = new Date().toLocaleDateString('en-US', { 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    });
    
    const tableRows = supplies.map(s => `
      <tr>
        <td style="border: 1pt solid #000; padding: 8px; text-align: center;">${s.stock_num}</td>
        <td style="border: 1pt solid #000; padding: 8px;">${s.item_desc}</td>
        <td style="border: 1pt solid #000; padding: 8px;">${s.category?.category_name || ''}</td>
        <td style="border: 1pt solid #000; padding: 8px; text-align: center;">${s.unit?.unit_name || ''}</td>
        <td style="border: 1pt solid #000; padding: 8px; text-align: center;">${s.status}</td>
        <td style="border: 1pt solid #000; padding: 8px; text-align: center; font-family: monospace;">${s.quantity}</td>
      </tr>
    `).join('');

    const htmlContent = `
      <div style="padding: 40px; font-family: 'Times New Roman', Times, serif; color: #000; background: #fff;">
        <!-- Header -->
        <div style="text-align: center; position: relative; margin-bottom: 40px;">
          <img src="assets/pup_logo.png" alt="Logo" style="position: absolute; left: 0; top: 0; height: 65px;">
          <div style="display: inline-block;">
            <h2 style="margin: 0; font-weight: bold; font-size: 16pt;">POLYTECHNIC UNIVERSITY OF THE PHILIPPINES</h2>
            <p style="margin: 0; font-size: 12pt;">Taguig Campus</p>
            <h3 style="margin: 20px 0 0 0; font-weight: bold; text-transform: uppercase; font-size: 14pt;">Supply Management Office</h3>
          </div>
          <hr style="border: 1pt solid #000; margin-top: 30px; opacity: 1;">
          <h2 style="font-weight: bold; margin-top: 30px; font-size: 18pt;">SUPPLY INVENTORY REPORT</h2>
          <p style="margin: 0; font-size: 12pt;">As of ${dateStr}</p>
        </div>

        <!-- Table -->
        <table style="width: 100%; border-collapse: collapse; border: 1pt solid #000; font-size: 11pt;">
          <thead>
            <tr style="background-color: #f2f2f2;">
              <th style="border: 1pt solid #000; padding: 10px; width: 15%;">Stock Number</th>
              <th style="border: 1pt solid #000; padding: 10px; width: 35%;">Item Description</th>
              <th style="border: 1pt solid #000; padding: 10px; width: 15%;">Category</th>
              <th style="border: 1pt solid #000; padding: 10px; width: 10%;">Unit</th>
              <th style="border: 1pt solid #000; padding: 10px; width: 15%;">Status</th>
              <th style="border: 1pt solid #000; padding: 10px; width: 10%;">Quantity</th>
            </tr>
          </thead>
          <tbody>
            ${tableRows || '<tr><td colspan="6" style="text-align: center; padding: 20px;">No supplies found.</td></tr>'}
          </tbody>
          <tfoot>
            <tr style="font-weight: bold; background-color: #f2f2f2;">
              <td colspan="5" style="border: 1pt solid #000; padding: 10px; text-align: right;">TOTAL ITEMS:</td>
              <td style="border: 1pt solid #000; padding: 10px; text-align: center;">${this.totalQuantity()}</td>
            </tr>
          </tfoot>
        </table>

        <!-- Signatories -->
        <div style="margin-top: 60px; padding: 0 40px; display: flex; justify-content: flex-end;">
          <div style="width: 250px;">
            <p style="margin-bottom: 50px;">Prepared by:</p>
            <div style="border-bottom: 1pt solid #000; text-align: center;">
              <strong style="text-transform: uppercase; font-size: 12pt;">${user?.first_name || ''} ${user?.last_name || ''}</strong>
            </div>
            <p style="text-align: center; margin-top: 5px;">${user?.office?.office_name || ''}</p>
          </div>
        </div>

        <div style="margin-top: 50px; text-align: center; color: #666; font-size: 9pt;">
          <p>Generated on ${new Date().toLocaleString()}</p>
        </div>
      </div>
    `;

    const iframe = document.createElement('iframe');
    iframe.style.position = 'fixed';
    iframe.style.right = '0';
    iframe.style.bottom = '0';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = '0';
    document.body.appendChild(iframe);

    const idoc = iframe.contentWindow?.document;
    if (!idoc) return;

    idoc.open();
    idoc.write(`
      <!DOCTYPE html>
      <html>
        <head>
          <title>Supply Inventory Report</title>
          <style>
            @page { size: portrait; margin: 10mm; }
            body { margin: 0; padding: 0; }
            * { box-sizing: border-box; }
          </style>
        </head>
        <body>${htmlContent}</body>
      </html>
    `);
    idoc.close();

    let printed = false;
    const triggerPrint = () => {
      if (printed) return;
      printed = true;
      setTimeout(() => {
        iframe.contentWindow?.focus();
        iframe.contentWindow?.print();
        setTimeout(() => {
          document.body.removeChild(iframe);
        }, 1000);
      }, 500);
    };

    iframe.onload = triggerPrint;
    // Fallback if onload doesn't trigger
    setTimeout(triggerPrint, 2000);
  }
}

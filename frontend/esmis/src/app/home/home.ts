import { Component, inject, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Sidebar } from "../sidebar/sidebar";
import { AuthService } from '../../services/auth.service';
import { SupplyService } from '../../services/supply.service';
import { Supply, Category, Unit } from '../../models/smis.model';

@Component({
  selector: 'app-home',
  imports: [Sidebar, CommonModule, ReactiveFormsModule],
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
}

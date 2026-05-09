import { Component, inject, OnInit, signal, computed } from '@angular/core';
import { Sidebar } from "../sidebar/sidebar";
import { SupplyService } from '../../services/supply.service';
import { AuthService } from '../../services/auth.service';
import { Supply } from '../../models/smis.model';
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

  availableSupplies: Supply[] = [];
  requestList: (Supply & { quantity_req: number })[] = [];
  purpose = '';
  purposeError = signal(false);
  notifMessage = signal('');
  notifType = signal<'success' | 'error' | 'warning'>('success');
  notifTitle = signal('');
  notifIcon = computed(() => this.notifType() === 'success' ? 'check-circle' : this.notifType() === 'error' ? 'x-circle' : 'exclamation-triangle');

  ngOnInit() {
    this.loadSupplies();
  }

  loadSupplies() {
    this.supplyService.listSupplies().subscribe({
      next: (supplies) => {
        this.availableSupplies = supplies;
      },
      error: (err) => console.error('Error fetching supplies', err)
    });
  }

  openModal() {
    const modalElement = document.getElementById('supplyInventoryModal');
    if (modalElement) {
      const modal = (window as any).bootstrap.Modal.getOrCreateInstance(modalElement);
      modal.show();
    }
  }

  showNotification(message: string, type: 'success' | 'error' | 'warning') {
    this.notifMessage.set(message);
    this.notifType.set(type);
    this.notifTitle.set(type === 'success' ? 'Success' : type === 'error' ? 'Error' : 'Warning');
    const modalElement = document.getElementById('notifModal-create-request');
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

  addItem(supply: Supply) {
    const existing = this.requestList.find(s => s.stock_num === supply.stock_num);
    if (!existing) {
      this.requestList.push({ ...supply, quantity_req: 1 });
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
      this.showNotification('You must be logged in to submit a request.', 'warning');
      return;
    }

    const requests = this.requestList.map(item => {
      return this.supplyService.createSupplyRequest({
        user_id: user.id,
        supply_id: item.stock_num,
        quantity_req: item.quantity_req,
        purpose: this.purpose
      });
    });

    forkJoin(requests).subscribe({
      next: (responses) => {
        this.showNotification('Request submitted successfully!', 'success');
        this.requestList = [];
        this.purpose = '';
      },
      error: (err) => {
        console.error('Error submitting requests', err);
        this.showNotification('There was an error submitting your request. Please check the console.', 'error');
      }
    });
  }
}

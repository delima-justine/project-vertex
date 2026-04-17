import { Component, inject, OnInit } from '@angular/core';
import { Sidebar } from "../sidebar/sidebar";
import { SupplyService } from '../../services/supply.service';
import { AuthService } from '../../services/auth.service';
import { Supply } from '../../models/smis.model';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { forkJoin } from 'rxjs';

@Component({
  selector: 'app-create-request',
  standalone: true,
  imports: [Sidebar, CommonModule, FormsModule],
  templateUrl: './create-request.html',
  styleUrl: './create-request.scss',
})
export class CreateRequest implements OnInit {
  private supplyService = inject(SupplyService);
  private authService = inject(AuthService);

  availableSupplies: Supply[] = [];
  requestList: (Supply & { quantity_req: number })[] = [];
  purpose = '';

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

  submitRequest() {
    const user = this.authService.currentUser();
    if (!user) {
      alert('You must be logged in to submit a request.');
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
        alert('Request submitted successfully!');
        this.requestList = [];
        this.purpose = '';
      },
      error: (err) => {
        console.error('Error submitting requests', err);
        alert('There was an error submitting your request. Please check the console.');
      }
    });
  }
}

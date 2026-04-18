import { Component, inject, OnInit, signal, computed } from '@angular/core';
import { Sidebar } from "../sidebar/sidebar";
import { SupplyService } from '../../services/supply.service';
import { AuthService } from '../../services/auth.service';
import { SupplyRequest } from '../../models/smis.model';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-approved',
  standalone: true,
  imports: [Sidebar, CommonModule, FormsModule],
  templateUrl: './approved.html',
  styleUrl: './approved.scss',
})
export class Approved implements OnInit {
  private supplyService = inject(SupplyService);
  private authService = inject(AuthService);

  user = this.authService.currentUser;

  requests = signal<SupplyRequest[]>([]);
  searchTerm = signal('');

  filteredRequests = computed(() => {
    return this.requests().filter(req => {
      const search = this.searchTerm().toLowerCase();
      return !search || 
             req.supply?.item_desc.toLowerCase().includes(search) ||
             req.supply_id.toLowerCase().includes(search) ||
             req.user?.first_name.toLowerCase().includes(search) ||
             req.user?.last_name.toLowerCase().includes(search);
    });
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

  releaseRequest(request: SupplyRequest) {
    if (confirm(`Are you sure you want to release ${request.supply?.item_desc}?`)) {
      this.supplyService.updateSupplyRequest(request.id, {
        status: 'released'
      }).subscribe({
        next: () => {
          alert('Request released!');
          this.loadApprovedRequests();
        },
        error: (err) => {
          console.error('Error releasing request', err);
          alert('Failed to release request.');
        }
      });
    }
  }
}

import { Component, inject, OnInit, signal, computed } from '@angular/core';
import { Sidebar } from "../sidebar/sidebar";
import { SupplyService } from '../../services/supply.service';
import { AuthService } from '../../services/auth.service';
import { SupplyRequest } from '../../models/smis.model';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-pending',
  standalone: true,
  imports: [Sidebar, CommonModule, FormsModule],
  templateUrl: './pending.html',
  styleUrl: './pending.scss',
})
export class Pending implements OnInit {
  private supplyService = inject(SupplyService);
  private authService = inject(AuthService);

  requests = signal<SupplyRequest[]>([]);
  searchTerm = signal('');
  selectedOffice = signal('all');

  filteredRequests = computed(() => {
    return this.requests().filter(req => {
      const search = this.searchTerm().toLowerCase();
      const matchesSearch = !search || 
                            req.supply?.item_desc.toLowerCase().includes(search) ||
                            req.supply_id.toLowerCase().includes(search) ||
                            req.user?.first_name.toLowerCase().includes(search) ||
                            req.user?.last_name.toLowerCase().includes(search);

      const matchesOffice = this.selectedOffice() === 'all' || req.user?.office?.office_name === this.selectedOffice();

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

  loadPendingRequests() {
    this.supplyService.listSupplyRequests('pending').subscribe({
      next: (data) => this.requests.set(data),
      error: (err) => console.error('Error fetching pending requests', err)
    });
  }

  approveRequest(request: SupplyRequest) {
    const admin = this.authService.currentUser();
    if (!admin) return;

    if (confirm(`Are you sure you want to approve request for ${request.supply?.item_desc}?`)) {
      this.supplyService.updateSupplyRequest(request.id, {
        status: 'approved',
        approved_by: admin.id
      }).subscribe({
        next: () => {
          alert('Request approved successfully!');
          this.loadPendingRequests();
        },
        error: (err) => {
          console.error('Error approving request', err);
          alert('Failed to approve request.');
        }
      });
    }
  }

  disapproveRequest(request: SupplyRequest) {
    if (confirm(`Are you sure you want to disapprove request for ${request.supply?.item_desc}?`)) {
      this.supplyService.updateSupplyRequest(request.id, {
        status: 'declined'
      }).subscribe({
        next: () => {
          alert('Request disapproved.');
          this.loadPendingRequests();
        },
        error: (err) => {
          console.error('Error disapproving request', err);
          alert('Failed to disapprove request.');
        }
      });
    }
  }
}

import { Component, computed, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Sidebar } from "../sidebar/sidebar";
import { TopNav } from "../top-nav/top-nav";
import { SupplyService } from '../../services/supply.service';
import { SupplyRequest } from '../../models/smis.model';

@Component({
  selector: 'app-reports',
  standalone: true,
  imports: [Sidebar, TopNav, CommonModule, FormsModule],
  templateUrl: './reports.html',
  styleUrl: './reports.scss',
})
export class Reports {
  private supplyService = inject(SupplyService);

  timePeriod = signal<'today' | 'week' | 'month' | 'custom'>('today');
  selectedStatus = signal('');
  selectedOffice = signal('');
  startDate = signal('');
  endDate = signal('');
  activeView = signal<'default' | 'request_logs' | 'admin_audit'>('default');
  allRequests = signal<SupplyRequest[]>([]);

  filteredRequests = computed(() => {
    const now = new Date();

    return this.allRequests().filter((req) => {
      const created = new Date(req.created_at);
      let matchesTime = true;

      switch (this.timePeriod()) {
        case 'today':
          matchesTime = created.toDateString() === now.toDateString();
          break;
        case 'week': {
          const sevenDaysAgo = new Date(now);
          sevenDaysAgo.setDate(now.getDate() - 6);
          sevenDaysAgo.setHours(0, 0, 0, 0);
          matchesTime = created >= sevenDaysAgo && created <= now;
          break;
        }
        case 'month':
          matchesTime = created.getMonth() === now.getMonth() && created.getFullYear() === now.getFullYear();
          break;
        case 'custom': {
          const start = this.startDate() ? new Date(this.startDate()) : null;
          const end = this.endDate() ? new Date(this.endDate()) : null;
          if (start) {
            start.setHours(0, 0, 0, 0);
          }
          if (end) {
            end.setHours(23, 59, 59, 999);
          }
          if (start && created < start) {
            matchesTime = false;
          }
          if (end && created > end) {
            matchesTime = false;
          }
          break;
        }
      }

      const matchesStatus = !this.selectedStatus() || req.status === this.selectedStatus();
      const matchesOffice = !this.selectedOffice() || req.user?.office?.office_name === this.selectedOffice();

      return matchesTime && matchesStatus && matchesOffice;
    });
  });

  showCustomRange = computed(() => this.timePeriod() === 'custom');

  exportToExcel() {
    console.log('Exporting to Excel...');
  }

  setTimePeriod(period: 'today' | 'week' | 'month' | 'custom') {
    this.timePeriod.set(period);
  }

  applyFilters() {
    console.log('Applying filters', {
      timePeriod: this.timePeriod(),
      status: this.selectedStatus(),
      office: this.selectedOffice(),
      startDate: this.startDate(),
      endDate: this.endDate()
    });
  }

  resetFilters() {
    this.timePeriod.set('today');
    this.selectedStatus.set('');
    this.selectedOffice.set('');
    this.startDate.set('');
    this.endDate.set('');
  }

  openRequestLogs() {
    this.activeView.set('request_logs');
    this.loadSupplyRequests();
  }

  openAdminAudit() {
    this.activeView.set('admin_audit');
    console.log('Opening Admin Audit...');
  }

  loadSupplyRequests() {
    this.supplyService.listSupplyRequests().subscribe({
      next: (data) => this.allRequests.set(data),
      error: (err) => console.error('Error loading supply requests', err)
    });
  }

  archiveRequest(req: SupplyRequest) {
    this.supplyService.createArchive(req.id).subscribe({
      next: () => {
        this.allRequests.set(this.allRequests().filter((item) => item.id !== req.id));
        alert('Request archived successfully.');
      },
      error: (err) => {
        console.error('Error archiving request', err);
        alert('Failed to archive the request.');
      }
    });
  }

  trackByRequestId(_index: number, request: SupplyRequest) {
    return request.id;
  }

  openArchive() {
    console.log('Opening Archive...');
  }

  archiveNow() {
    console.log('Archiving current results...');
  }
}

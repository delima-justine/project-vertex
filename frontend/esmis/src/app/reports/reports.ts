import { Component, computed, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Sidebar } from "../sidebar/sidebar";
import { TopNav } from "../top-nav/top-nav";
import { SupplyService } from '../../services/supply.service';
import { Archive, SupplyRequest } from '../../models/smis.model';
import { forkJoin } from 'rxjs';

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
  activeView = signal<'request_logs' | 'admin_audit'>('request_logs');
  allRequests = signal<SupplyRequest[]>([]);
  archivedRecords = signal<Archive[]>([]);
  archiveModalOpen = signal(false);
  selectedArchiveIds = signal<number[]>([]);
  archiveSelectMode = signal<'current' | 'all'>('current');
  archivePage = signal(1);
  readonly archivePageSize = 10;

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

  archivePageCount = computed(() => {
    return Math.max(1, Math.ceil(this.archivedRecords().length / this.archivePageSize));
  });

  archivePageNumbers = computed(() => {
    return Array.from({ length: this.archivePageCount() }, (_, index) => index + 1);
  });

  paginatedArchives = computed(() => {
    const archives = this.archivedRecords();
    const start = (this.archivePage() - 1) * this.archivePageSize;
    return archives.slice(start, start + this.archivePageSize);
  });

  selectedArchiveCount = computed(() => this.selectedArchiveIds().length);
  totalArchivedCount = computed(() => this.archivedRecords().length);

  archivePageStart = computed(() => {
    if (this.totalArchivedCount() === 0) {
      return 0;
    }
    return (this.archivePage() - 1) * this.archivePageSize + 1;
  });

  archivePageEnd = computed(() => {
    return Math.min(this.archivePage() * this.archivePageSize, this.totalArchivedCount());
  });

  allArchivedSelected = computed(() => {
    const selectedIds = this.selectedArchiveIds();
    if (selectedIds.length === 0) {
      return false;
    }

    if (this.archiveSelectMode() === 'current') {
      const currentPageIds = this.paginatedArchives().map((archive) => archive.id);
      return currentPageIds.length > 0 && currentPageIds.every((id) => selectedIds.includes(id));
    }

    return selectedIds.length === this.totalArchivedCount() && this.totalArchivedCount() > 0;
  });

  constructor() {
    this.loadSupplyRequests();
  }

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

  loadArchives() {
    this.supplyService.listArchives().subscribe({
      next: (data) => {
        this.archivedRecords.set(data);
        this.selectedArchiveIds.set([]);
        this.archivePage.set(1);
        this.archiveSelectMode.set('current');
      },
      error: (err) => console.error('Error loading archives', err)
    });
  }

  openArchiveModal() {
    this.loadArchives();
    this.archiveModalOpen.set(true);
  }

  closeArchiveModal() {
    this.archiveModalOpen.set(false);
  }

  setArchiveSelectMode(mode: 'current' | 'all') {
    const wasAllSelected = this.allArchivedSelected();
    this.archiveSelectMode.set(mode);

    if (wasAllSelected) {
      const ids = mode === 'all'
        ? this.archivedRecords().map((archive) => archive.id)
        : this.paginatedArchives().map((archive) => archive.id);
      this.selectedArchiveIds.set(ids);
    }
  }

  toggleArchiveSelectAll() {
    if (this.allArchivedSelected()) {
      this.selectedArchiveIds.set([]);
      return;
    }

    const selectedIds = this.archiveSelectMode() === 'all'
      ? this.archivedRecords().map((archive) => archive.id)
      : this.paginatedArchives().map((archive) => archive.id);

    this.selectedArchiveIds.set(selectedIds);
  }

  toggleArchiveSelection(archiveId: number) {
    const current = this.selectedArchiveIds();
    if (current.includes(archiveId)) {
      this.selectedArchiveIds.set(current.filter((id) => id !== archiveId));
      return;
    }
    this.selectedArchiveIds.set([...current, archiveId]);
  }

  setArchivePage(page: number) {
    if (page < 1 || page > this.archivePageCount()) {
      return;
    }
    this.archivePage.set(page);
  }

  archiveFilteredResults() {
    const releasedRequests = this.filteredRequests().filter((req) => req.status === 'released');

    if (releasedRequests.length === 0) {
      alert('No released records are available in the current filtered results.');
      return;
    }

    forkJoin(releasedRequests.map((req) => this.supplyService.createArchive(req.id))).subscribe({
      next: () => {
        this.loadSupplyRequests();
        this.loadArchives();
        alert(`${releasedRequests.length} released request(s) archived successfully.`);
      },
      error: (err) => {
        console.error('Error archiving filtered results', err);
        alert('Failed to archive one or more records.');
      }
    });
  }

  archiveSingle(req: SupplyRequest) {
    this.supplyService.createArchive(req.id).subscribe({
      next: () => {
        this.allRequests.set(this.allRequests().filter((item) => item.id !== req.id));
        this.loadArchives();
        alert('Request archived successfully.');
      },
      error: (err) => {
        console.error('Error archiving request', err);
        alert('Failed to archive the request.');
      }
    });
  }

  restoreArchive(archive: Archive) {
    this.supplyService.restoreArchive(archive.id).subscribe({
      next: () => {
        this.archivedRecords.set(this.archivedRecords().filter((item) => item.id !== archive.id));
        this.loadSupplyRequests();
        this.selectedArchiveIds.set(this.selectedArchiveIds().filter((id) => id !== archive.id));
        alert('Archive restored successfully.');
      },
      error: (err) => {
        console.error('Error restoring archive', err);
        alert('Failed to restore the archive.');
      }
    });
  }

  restoreSelectedArchives() {
    const selectedIds = this.selectedArchiveIds();
    if (selectedIds.length === 0) {
      return;
    }

    forkJoin(selectedIds.map((archiveId) => this.supplyService.restoreArchive(archiveId))).subscribe({
      next: () => {
        this.loadArchives();
        this.loadSupplyRequests();
        this.selectedArchiveIds.set([]);
        alert(`${selectedIds.length} archived record(s) restored successfully.`);
      },
      error: (err) => {
        console.error('Error restoring selected archives', err);
        alert('Failed to restore selected archived records.');
      }
    });
  }

  trackByRequestId(_index: number, request: SupplyRequest) {
    return request.id;
  }
}

import { Component, computed, inject, signal, ViewChild, ElementRef, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import * as ExcelJS from 'exceljs';
import { Sidebar } from "../sidebar/sidebar";
import { TopNav } from "../top-nav/top-nav";
import { SupplyService } from '../../services/supply.service';
import { AuditService } from '../../services/audit.service';
import { UserManagementService } from '../../services/user-management.service';
import { AdminAudit, Archive, Office, SupplyRequest, User } from '../../models/smis.model';
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
  private auditService = inject(AuditService);
  private userService = inject(UserManagementService);

  timePeriod = signal<'today' | 'week' | 'month' | 'custom'>('today');
  selectedStatus = signal('');
  selectedOffice = signal('');
  startDate = signal('');
  endDate = signal('');
  appliedStatus = signal('');
  appliedOffice = signal('');
  appliedStartDate = signal('');
  appliedEndDate = signal('');
  appliedTimePeriod = signal<'today' | 'week' | 'month' | 'custom'>('today');
  activeView = signal<'request_logs' | 'admin_audit'>('request_logs');
  allRequests = signal<SupplyRequest[]>([]);
  allOffices = signal<Office[]>([]);
  allAdmins = signal<User[]>([]);
  archivedRecords = signal<Archive[]>([]);
  selectedArchiveIds = signal<number[]>([]);
  archiveSelectMode = signal<'current' | 'all'>('current');
  archivePage = signal(1);
  readonly archivePageSize = 10;

  // Admin Audit signals
  adminAudits = signal<AdminAudit[]>([]);
  auditPage = signal(1);
  auditLastPage = signal(1);
  auditTotal = signal(0);
  auditLimit = 15;
  isAuditLoading = signal(false);
  selectedActionType = signal('');
  appliedActionType = signal('');
  selectedAdminId = signal<number | null>(null);
  appliedAdminId = signal<number | null>(null);

  notifMessage = signal('');
  notifType = signal<'success' | 'error' | 'warning'>('success');
  notifTitle = signal('');
  notifIcon = computed(() => {
    switch (this.notifType()) {
      case 'success': return 'bi-check-circle-fill';
      case 'error': return 'bi-exclamation-triangle-fill';
      case 'warning': return 'bi-exclamation-triangle-fill';
      default: return 'bi-info-circle-fill';
    }
  });

  @ViewChild('archiveModal', { static: false }) archiveModalElement?: ElementRef<HTMLElement>;
  private cdr = inject(ChangeDetectorRef);

  private getModalInstance() {
    if (!this.archiveModalElement) return null;
    const bootstrap = (window as any).bootstrap;
    if (bootstrap) {
      return bootstrap.Modal.getOrCreateInstance(this.archiveModalElement.nativeElement);
    }
    return null;
  }

  filteredRequests = computed(() => {
    const now = new Date();

    return this.allRequests().filter((req) => {
      const created = new Date(req.created_at);
      let matchesTime = true;

      switch (this.appliedTimePeriod()) {
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
          const end = this.appliedEndDate() ? new Date(this.appliedEndDate()) : null;
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

      const matchesStatus = !this.appliedStatus() || req.status === this.appliedStatus();
      const matchesOffice = !this.appliedOffice() || req.user?.office?.office_name === this.appliedOffice();

      return matchesTime && matchesStatus && matchesOffice;
    });
  });

  showCustomRange = computed(() => this.timePeriod() === 'custom');

  showNotification(message: string, type: 'success' | 'error' | 'warning') {
    this.notifMessage.set(message);
    this.notifType.set(type);
    this.notifTitle.set(type === 'success' ? 'Success' : type === 'error' ? 'Error' : 'Warning');
    this.cdr.detectChanges();
    const modalElement = document.getElementById('notifModal-reports');
    if (modalElement) {
      setTimeout(() => {
        const modal = (window as any).bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
      }, 0);
    }
  }

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
    this.loadOffices();
    this.loadAdmins();
  }

  loadOffices() {
    this.supplyService.listOffices().subscribe({
      next: (data) => this.allOffices.set(data),
      error: (err) => console.error('Error loading offices', err)
    });
  }

  loadAdmins() {
    this.userService.listAdmins().subscribe({
      next: (data) => this.allAdmins.set(data),
      error: (err) => console.error('Error loading admins', err)
    });
  }

  exportToExcel() {
    this.generateExcelReport();
  }

  exportArchivePdf() {
    this.generateArchivePdf();
  }

  private async generateArchivePdf() {
    const selectedIds = this.selectedArchiveIds();
    const archives = selectedIds.length > 0
      ? this.archivedRecords().filter((archive) => selectedIds.includes(archive.id))
      : this.archiveSelectMode() === 'current'
        ? this.paginatedArchives()
        : this.archivedRecords();

    if (archives.length === 0) {
      this.showNotification('No archive records available to export.', 'warning');
      return;
    }

    try {
      let logoBase64 = '';
      try {
        const logoResponse = await fetch('assets/pup_logo.png');
        const logoBlob = await logoResponse.blob();
        logoBase64 = await new Promise<string>((resolve) => {
          const reader = new FileReader();
          reader.onloadend = () => resolve(reader.result as string);
          reader.readAsDataURL(logoBlob);
        });
      } catch {}

      const h2p = await import('html2pdf.js');
      const html2pdf = (h2p as any).default || h2p;

      const rowsHtml = archives
        .map((archive, index) => {
          const archivedBy = archive.archiver
            ? `${archive.archiver.first_name} ${archive.archiver.last_name}`.trim()
            : '';
          const archivedDate = archive.archived_at
            ? new Date(archive.archived_at).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
              })
            : '';
          const typeLabel = archive.table_name === 'tbl_request' ? 'REQUESTS' : archive.table_name.toUpperCase();
          const description = archive.data?.supply
            ? `Request for ${archive.data.supply.item_desc} (Qty: ${archive.data.quantity_req || 0})`
            : `Request ${archive.original_id}`;
          return `
            <tr style="background-color: ${index % 2 === 0 ? '#ffffff' : '#f8f9fa'};">
              <td style="border:1px solid #ccc; padding:6px; text-align:center; font-size:10pt;">${typeLabel}</td>
              <td style="border:1px solid #ccc; padding:6px; text-align:center; font-size:10pt;">${archive.original_id}</td>
              <td style="border:1px solid #ccc; padding:6px; text-align:left; font-size:10pt;">${description}</td>
              <td style="border:1px solid #ccc; padding:6px; text-align:center; font-size:10pt;">${archive.data?.status || ''}</td>
              <td style="border:1px solid #ccc; padding:6px; text-align:left; font-size:10pt;">${archivedDate}</td>
              <td style="border:1px solid #ccc; padding:6px; text-align:left; font-size:10pt;">${archivedBy}</td>
            </tr>
          `;
        })
        .join('');

      const html = `
        <div style="font-family: Arial, sans-serif; padding: 16px;">
          <div style="display: flex; align-items: center; justify-content: center; gap: 16px; margin-bottom: 12px;">
            ${logoBase64 ? `<img src="${logoBase64}" style="width: 64px; height: auto; object-fit: contain;" />` : ''}
            <div style="text-align: left;">
              <div style="font-size: 14pt; font-weight: 700;">Polytechnic University of the Philippines</div>
              <div style="font-size: 12pt; margin-top: 4px;">Taguig Campus</div>
            </div>
          </div>
          <div style="border-top: 1px solid #333; margin: 0 0 10px 0;"></div>
          <div style="text-align: center; margin-bottom: 14px; font-size: 12pt; font-weight: 700;">Archived Records - Supply Management Information System</div>
          <table style="width: 100%; border-collapse: collapse; font-size: 10pt;">
            <thead>
              <tr style="background-color: #800000; color: #ffffff;">
                <th style="border:1px solid #ccc; padding:10px 8px; text-align:center; width:90px;">Type</th>
                <th style="border:1px solid #ccc; padding:10px 8px; text-align:center; width:90px;">Reference</th>
                <th style="border:1px solid #ccc; padding:10px 8px; text-align:left;">Description</th>
                <th style="border:1px solid #ccc; padding:10px 8px; text-align:center; width:90px;">Status</th>
                <th style="border:1px solid #ccc; padding:10px 8px; text-align:left; width:150px;">Archived Date</th>
                <th style="border:1px solid #ccc; padding:10px 8px; text-align:left; width:150px;">Archived By</th>
              </tr>
            </thead>
            <tbody>
              ${rowsHtml}
            </tbody>
          </table>
        </div>
      `;

      const wrapper = document.createElement('div');
      wrapper.innerHTML = html;

      await html2pdf()
        .set({
          margin: [10, 10, 10, 10],
          filename: `Archived_Records_${new Date().toISOString().slice(0, 10)}.pdf`,
          image: { type: 'jpeg', quality: 0.98 },
          html2canvas: { scale: 2, useCORS: true },
          jsPDF: { unit: 'pt', format: 'a4', orientation: 'portrait' },
        })
        .from(wrapper)
        .save();
    } catch (error) {
      console.error('Error exporting archive PDF:', error);
      this.showNotification('Failed to export archive PDF.', 'error');
    }
  }

  private async generateExcelReport() {
    try {
      const requests = this.filteredRequests();
      if (requests.length === 0) {
        this.showNotification('No data available to export.', 'warning');
        return;
      }

      const workbook = new ExcelJS.Workbook();
      const worksheet = workbook.addWorksheet('Request Logs');

      let logoBase64 = '';
      try {
        const logoResponse = await fetch('assets/pup_logo.png');
        const logoBlob = await logoResponse.blob();
        logoBase64 = await new Promise<string>((resolve) => {
          const reader = new FileReader();
          reader.onloadend = () => resolve(reader.result as string);
          reader.readAsDataURL(logoBlob);
        });
      } catch {}

      if (logoBase64) {
        const imageId = workbook.addImage({
          base64: logoBase64,
          extension: 'png',
        });
        worksheet.addImage(imageId, {
          tl: { col: 0, row: 0 },
          ext: { width: 80, height: 80 }
        });
      }

      worksheet.mergeCells('B1:G1');
      worksheet.mergeCells('B2:G2');
      worksheet.mergeCells('B3:G3');
      worksheet.mergeCells('B4:G4');

      const row1 = worksheet.getCell('B1');
      row1.value = 'Republic of the Philippines';
      row1.font = { size: 10, bold: false };
      row1.alignment = { horizontal: 'center', vertical: 'middle' };

      const row2 = worksheet.getCell('B2');
      row2.value = 'Polytechnic University of the Philippines';
      row2.font = { size: 13, bold: true };
      row2.alignment = { horizontal: 'center', vertical: 'middle' };

      const row3 = worksheet.getCell('B3');
      row3.value = 'Office of the Vice President for Campuses';
      row3.font = { size: 10, bold: false };
      row3.alignment = { horizontal: 'center', vertical: 'middle' };

      const row4 = worksheet.getCell('B4');
      row4.value = 'Taguig Campus';
      row4.font = { size: 11, bold: true };
      row4.alignment = { horizontal: 'center', vertical: 'middle' };

      worksheet.mergeCells('A5:G5');
      worksheet.mergeCells('A6:G6');
      const titleCell = worksheet.getCell('A6');
      titleCell.value = 'Supply Management Information System — Request Logs Report';
      titleCell.font = { size: 13, bold: true };
      titleCell.alignment = { horizontal: 'center', vertical: 'middle' };

      worksheet.mergeCells('A7:G7');
      const dateRangeCell = worksheet.getCell('A7');
      const startDateStr = this.startDate() || 'All';
      const endDateStr = this.endDate() || 'All';
      dateRangeCell.value = `Date Range: ${startDateStr} to ${endDateStr}`;
      dateRangeCell.alignment = { horizontal: 'center', vertical: 'middle' };

      worksheet.mergeCells('A8:G8');
      const generatedCell = worksheet.getCell('A8');
      const today = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
      generatedCell.value = `Report Generated On: ${today}`;
      generatedCell.alignment = { horizontal: 'center', vertical: 'middle' };

      const headerRow = worksheet.getRow(11);
      headerRow.values = ['STOCK #', 'OFFICE', 'SUPPLY NAME', 'REQUESTED QTY', 'STATUS', 'CREATED AT', 'UPDATED AT'];
      headerRow.font = { bold: true, color: { argb: 'FFFFFFFF' } };
      headerRow.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF9B1C1C' } };
      headerRow.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };

      headerRow.eachCell((cell) => {
        cell.border = {
          top: { style: 'thin' },
          left: { style: 'thin' },
          bottom: { style: 'thin' },
          right: { style: 'thin' }
        };
      });

      const statusColorMap: { [key: string]: string } = {
        'approved': 'FF217346',
        'disapproved': 'FF9B1C1C',
        'released': 'FF1F4E79',
        'pending': 'FFC55A11'
      };

      requests.forEach((req, index) => {
        const rowNum = 12 + index;
        const row = worksheet.getRow(rowNum);

        row.values = [
          req.supply_id,
          req.user?.office?.office_name || '',
          req.supply?.item_desc || '',
          req.quantity_req,
          req.status,
          req.created_at ? new Date(req.created_at).toLocaleDateString('en-US') : '',
          req.updated_at ? new Date(req.updated_at).toLocaleDateString('en-US') : ''
        ];

        const isEvenRow = (index + 1) % 2 === 0;
        const bgColor = isEvenRow ? 'FFE2EFDA' : 'FFFFFFFF';

        row.eachCell((cell, colNum) => {
          cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: bgColor } };
          cell.border = {
            top: { style: 'thin' },
            left: { style: 'thin' },
            bottom: { style: 'thin' },
            right: { style: 'thin' }
          };
          cell.alignment = { horizontal: 'left', vertical: 'middle' };

          if (colNum === 5) {
            const statusColor = statusColorMap[req.status.toLowerCase()];
            if (statusColor) {
              cell.font = { color: { argb: statusColor }, bold: true };
            }
            cell.alignment = { horizontal: 'center', vertical: 'middle' };
          }
        });
      });

      worksheet.columns = [
        { width: 12 },
        { width: 22 },
        { width: 30 },
        { width: 15 },
        { width: 14 },
        { width: 22 },
        { width: 22 }
      ];

      const fileName = this.startDate() && this.endDate()
        ? `Request_Logs_${this.startDate()}_to_${this.endDate()}.xlsx`
        : 'Request_Logs_All.xlsx';

      const buffer = await workbook.xlsx.writeBuffer();
      const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = fileName;
      document.body.appendChild(link);
      link.click();
      link.remove();
      window.URL.revokeObjectURL(url);

      console.log(`Excel file exported: ${fileName}`);
    } catch (error) {
      console.error('Error exporting to Excel:', error);
      this.showNotification('Failed to export to Excel.', 'error');
    }
  }

  setTimePeriod(period: 'today' | 'week' | 'month' | 'custom') {
    this.timePeriod.set(period);
    this.appliedTimePeriod.set(period);

    if (period !== 'custom') {
      this.appliedStartDate.set('');
      this.appliedEndDate.set('');
    }
  }

  applyFilters() {
    this.appliedStatus.set(this.selectedStatus());
    this.appliedOffice.set(this.selectedOffice());
    this.appliedActionType.set(this.selectedActionType());
    this.appliedAdminId.set(this.selectedAdminId());
    this.appliedTimePeriod.set(this.timePeriod());

    if (this.timePeriod() === 'custom') {
      this.appliedStartDate.set(this.startDate());
      this.appliedEndDate.set(this.endDate());
    } else {
      this.appliedStartDate.set('');
      this.appliedEndDate.set('');
    }

    if (this.activeView() === 'admin_audit') {
      this.loadAudits(1);
    }
  }

  resetFilters() {
    this.timePeriod.set('today');
    this.selectedStatus.set('');
    this.selectedOffice.set('');
    this.selectedActionType.set('');
    this.selectedAdminId.set(null);
    this.startDate.set('');
    this.endDate.set('');
    
    if (this.activeView() === 'admin_audit') {
      this.loadAudits(1);
    }
  }

  openRequestLogs() {
    this.activeView.set('request_logs');
    this.loadSupplyRequests();
  }

  openAdminAudit() {
    this.activeView.set('admin_audit');
    this.loadAudits(1);
  }

  loadAudits(page = 1) {
    this.isAuditLoading.set(true);
    
    const filters = {
      page,
      limit: this.auditLimit,
      action_type: this.appliedActionType(),
      admin_id: this.appliedAdminId() || undefined
    };

    this.auditService.listAudits(filters).subscribe({
      next: (response) => {
        this.adminAudits.set(response.data);
        this.auditPage.set(response.current_page);
        this.auditLastPage.set(response.last_page);
        this.auditTotal.set(response.total);
        this.isAuditLoading.set(false);
      },
      error: (err) => {
        console.error('Error loading audits', err);
        this.isAuditLoading.set(false);
      }
    });
  }

  auditNextPage() {
    if (this.auditPage() < this.auditLastPage()) {
      this.loadAudits(this.auditPage() + 1);
    }
  }

  auditPrevPage() {
    if (this.auditPage() > 1) {
      this.loadAudits(this.auditPage() - 1);
    }
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
    this.getModalInstance()?.show();
  }

  closeArchiveModal() {
    this.getModalInstance()?.hide();
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
    const requestsToArchive = this.filteredRequests();

    if (requestsToArchive.length === 0) {
      alert('No records are available in the current filtered results.');
      return;
    }

    if (!confirm(`Are you sure you want to archive ${requestsToArchive.length} record(s)?`)) {
      return;
    }

    forkJoin(requestsToArchive.map((req) => this.supplyService.createArchive(req.id))).subscribe({
      next: () => {
        this.loadSupplyRequests();
        this.loadArchives();
        this.showNotification(`${requestsToArchive.length} request(s) archived successfully.`, 'success');
      },
      error: (err) => {
        console.error('Error archiving filtered results', err);
        this.showNotification('Failed to archive one or more records.', 'error');
      }
    });
  }

  archiveSingle(req: SupplyRequest) {
    this.supplyService.createArchive(req.id).subscribe({
      next: () => {
        this.allRequests.set(this.allRequests().filter((item) => item.id !== req.id));
        this.loadArchives();
        this.showNotification('Request archived successfully.', 'success');
      },
      error: (err) => {
        console.error('Error archiving request', err);
        this.showNotification('Failed to archive the request.', 'error');
      }
    });
  }

  restoreArchive(archive: Archive) {
    this.supplyService.restoreArchive(archive.id).subscribe({
      next: () => {
        this.archivedRecords.set(this.archivedRecords().filter((item) => item.id !== archive.id));
        this.loadSupplyRequests();
        this.selectedArchiveIds.set(this.selectedArchiveIds().filter((id) => id !== archive.id));
        this.showNotification('Archive restored successfully.', 'success');
      },
      error: (err) => {
        console.error('Error restoring archive', err);
        this.showNotification('Failed to restore the archive.', 'error');
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
        this.showNotification(`${selectedIds.length} archived record(s) restored successfully.`, 'success');
      },
      error: (err) => {
        console.error('Error restoring selected archives', err);
        this.showNotification('Failed to restore selected archived records.', 'error');
      }
    });
  }

  trackByRequestId(_index: number, request: SupplyRequest) {
    return request.id;
  }
}

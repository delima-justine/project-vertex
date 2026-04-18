import { Component, ElementRef, inject, OnDestroy, OnInit, signal, ViewChild, computed } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { SupplyService } from '../../services/supply.service';
import { SupplyRequest } from '../../models/smis.model';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AuthService } from '../../services/auth.service';
import { interval, Subscription, forkJoin } from 'rxjs';

@Component({
  selector: 'app-edit-ris',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './edit-ris.html',
  styleUrl: './edit-ris.scss',
})
export class EditRis implements OnInit, OnDestroy {
  private route = inject(ActivatedRoute);
  private router = inject(Router);
  private supplyService = inject(SupplyService);
  private authService = inject(AuthService);

  requests = signal<SupplyRequest[]>([]);
  approver = signal(this.authService.currentUser());

  combinedPurposes = computed(() => {
    const purposes = this.requests()
      .map(r => r.purpose?.trim())
      .filter(p => !!p);
    return [...new Set(purposes)].join(', ');
  });

  totalItems = computed(() => {
    return this.requests().reduce((sum, r) => sum + r.quantity_req, 0);
  });

  dateNow = new Date();
  today = new Date(this.dateNow.getFullYear(), this.dateNow.getMonth(), this.dateNow.getDate());

  @ViewChild('confirmModal', { static: false }) confirmModalElement?: ElementRef<HTMLElement>;
  
  // Real-time processing counter
  elapsedSeconds = signal(0);
  private timerSubscription?: Subscription;
  
  // For each request, track its specific processing data
  requestData = signal<Record<number, { issueQty: number; remarks: string; stockAvailable: boolean }>>({});

  // For "Received By"
  receivedByName = signal<string>('');
  receivedByOffice = signal<string>('');

  isPrinted = signal<boolean>(false);

  ngOnInit() {
    const idParam = this.route.snapshot.paramMap.get('id');
    if (idParam) {
      const ids = idParam.split(',').map(id => Number(id.trim()));
      this.loadRequests(ids);
    }

    // Start counting from 0 when the component loads
    this.timerSubscription = interval(1000).subscribe(() => {
      this.elapsedSeconds.update(s => s + 1);
    });
  }

  ngOnDestroy() {
    this.timerSubscription?.unsubscribe();
  }

  loadRequests(ids: number[]) {
    const obs = ids.map(id => this.supplyService.getSupplyRequest(id));
    forkJoin(obs).subscribe({
      next: (data) => {
        this.requests.set(data);
        const initialData: Record<number, { issueQty: number; remarks: string; stockAvailable: boolean }> = {};
        data.forEach(req => {
          initialData[req.id] = {
            issueQty: req.quantity_req,
            remarks: '',
            stockAvailable: true
          };
        });
        this.requestData.set(initialData);
      },
      error: (err) => {
        console.error('Error fetching requests', err);
        alert('Failed to load request data.');
        this.router.navigate(['/pending-requests']);
      }
    });
  }

  cancel() {
    this.router.navigate(['/pending-requests']);
  }

  saveAndApprove() {
    const admin = this.approver();
    const reqs = this.requests();
    if (reqs.length === 0 || !admin) return;

    if (confirm(`Are you sure you want to approve these ${reqs.length} requests?`)) {
      const updates = reqs.map(req => {
        const data = this.requestData()[req.id];
        return this.supplyService.updateSupplyRequest(req.id, {
          status: 'approved',
          approved_by: admin.id,
          quantity_req: data.issueQty
        });
      });

      forkJoin(updates).subscribe({
        next: () => {
          alert('Requests approved successfully!');
          this.router.navigate(['/pending-requests']);
        },
        error: (err) => {
          console.error('Error approving requests', err);
          alert('Failed to approve one or more requests.');
        }
      });
    }
  }

  updateRequestData(id: number, field: string, value: any) {
    this.requestData.update(current => {
      const newData = { ...current };
      newData[id] = { ...newData[id], [field]: value };
      
      // If stockAvailable changed to false, set issueQty to 0
      if (field === 'stockAvailable' && value === false) {
        newData[id].issueQty = 0;
      } else if (field === 'stockAvailable' && value === true) {
        const req = this.requests().find(r => r.id === id);
        newData[id].issueQty = req?.quantity_req || 0;
      }
      
      return newData;
    });
  }

  getFormattedProcessingTime(): string {
    const totalSeconds = this.elapsedSeconds();
    const hrs = Math.floor(totalSeconds / 3600);
    const mins = Math.floor((totalSeconds % 3600) / 60);
    const secs = totalSeconds % 60;
    const formattedSecs = secs < 10 ? `0${secs}` : secs;
    if (hrs > 0) {
      const formattedMins = mins < 10 ? `0${mins}` : mins;
      return `${hrs}:${formattedMins}:${formattedSecs}`;
    }
    return `${mins}:${formattedSecs}`;
  }

  async printRIS() {
    const reqs = this.requests();
    if (reqs.length === 0) {
      alert('Error: No request data found.');
      return;
    }

    try {
      const h2p = await import('html2pdf.js');
      const html2pdf = (h2p as any).default || h2p;

      const firstReq = reqs[0];
      const fullName = ((firstReq.user?.first_name || '') + ' ' + (firstReq.user?.last_name || '')).toUpperCase();
      const office = (firstReq.user?.office?.office_name || '').toUpperCase();
      const purpose = this.combinedPurposes();

      const tableRows = reqs.map(req => {
        const data = this.requestData()[req.id];
        const stockYes = data.stockAvailable ? '&#9679;' : '&#9675;';
        const stockNo = !data.stockAvailable ? '&#9679;' : '&#9675;';
        return `
          <tr>
            <td style="border: 0.5pt solid #000; padding: 8px;">${req.supply_id || ''}</td>
            <td style="border: 0.5pt solid #000; padding: 8px;">${req.supply?.unit?.unit_name || ''}</td>
            <td style="border: 0.5pt solid #000; padding: 8px;">${req.supply?.item_desc || ''}</td>
            <td style="border: 0.5pt solid #000; padding: 8px; text-align: center;">${req.quantity_req || 0}</td>
            <td style="border: 0.5pt solid #000; padding: 8px; text-align: center; font-size: 14pt;">${stockYes}</td>
            <td style="border: 0.5pt solid #000; padding: 8px; text-align: center; font-size: 14pt;">${stockNo}</td>
            <td style="border: 0.5pt solid #000; padding: 8px; text-align: center;">${data.issueQty || ''}</td>
            <td style="border: 0.5pt solid #000; padding: 8px;">${data.remarks || ''}</td>
          </tr>
        `;
      }).join('');

      const pdfContent = `
        <div style="padding: 20px; font-family: Arial, sans-serif; color: #000; background: #fff; line-height: 1.2;">
          <div style="text-align: right; font-size: 11pt; margin-bottom: 30px;">
            <p style="margin: 0;">PUP-RISL-6-PSMO-010</p>
            <p style="margin: 0;">Rev. 2</p>
            <p style="margin: 0;">Effectivity Date: April 23, 2026</p>
            <p style="margin: 0;">Appendix 63</p>
          </div>
          <div style="text-align: center; font-size: 14pt; font-weight: bold; margin-bottom: 25px;">
            REQUISITION AND ISSUE SLIP
          </div>
          <table style="width: 100%; border-collapse: collapse; border: 1pt solid #000; font-size: 10pt;">
            <thead>
              <tr>
                <th colspan="4" style="border: 0.5pt solid #000; padding: 8px; text-align: left;">Requisition</th>
                <th colspan="2" style="border: 0.5pt solid #000; padding: 8px; text-align: left;">Stock Available?</th>
                <th colspan="2" style="border: 0.5pt solid #000; padding: 8px; text-align: left;">Issue</th>
              </tr>
              <tr>
                <th style="border: 0.5pt solid #000; padding: 5px; width: 12%;">Stock No.</th>
                <th style="border: 0.5pt solid #000; padding: 5px; width: 8%;">Unit</th>
                <th style="border: 0.5pt solid #000; padding: 5px; width: 40%;">Description</th>
                <th style="border: 0.5pt solid #000; padding: 5px; width: 10%;">Quantity</th>
                <th style="border: 0.5pt solid #000; padding: 5px; width: 7%; text-align: center;">Yes</th>
                <th style="border: 0.5pt solid #000; padding: 5px; width: 7%; text-align: center;">No</th>
                <th style="border: 0.5pt solid #000; padding: 5px; width: 8%; text-align: center;">Quantity</th>
                <th style="border: 0.5pt solid #000; padding: 5px;">Remarks</th>
              </tr>
            </thead>
            <tbody>
              ${tableRows}
            </tbody>
          </table>
          <div style="margin-top: 15px; font-size: 11pt; border-bottom: 1px solid #000; padding-bottom: 5px;">
            Purpose: ${purpose}
          </div>
          <table style="width: 100%; border-collapse: collapse; border: 1pt solid #000; margin-top: 40px; table-layout: fixed;">
            <tr>
              <td style="border: 0.5pt solid #000; padding: 12px 6px; text-align: center; width: 25%; vertical-align: top;">
                <div style="font-size: 10pt; margin-bottom: 8px;">Requested by:</div>
                <div style="width: 70%; margin: 8px auto 6px auto; border-top: 1pt solid #000; height: 0;"></div>
                <div style="font-weight: bold; font-size: 11pt; margin: 6px 0;">${fullName}</div>
                <div style="width: 70%; margin: 6px auto 8px auto; border-top: 1pt solid #000; height: 0;"></div>
                <div style="font-size: 9pt; margin-top: 6px;">${office}</div>
              </td>
              <td style="border: 0.5pt solid #000; padding: 12px 6px; text-align: center; width: 25%; vertical-align: top;">
                <div style="font-size: 10pt; margin-bottom: 8px;">Approved by:</div>
                <div style="width: 70%; margin: 8px auto 6px auto; border-top: 1pt solid #000; height: 0;"></div>
                <div style="font-weight: bold; font-size: 11pt; margin: 6px 0;">DR. MARISSA B. FERRER</div>
                <div style="width: 70%; margin: 6px auto 8px auto; border-top: 1pt solid #000; height: 0;"></div>
                <div style="font-size: 9pt; margin-top: 6px;">DIRECTOR</div>
              </td>
              <td style="border: 0.5pt solid #000; padding: 12px 6px; text-align: center; width: 25%; vertical-align: top;">
                <div style="font-size: 10pt; margin-bottom: 8px;">Issued by:</div>
                <div style="width: 70%; margin: 8px auto 6px auto; border-top: 1pt solid #000; height: 0;"></div>
                <div style="font-weight: bold; font-size: 11pt; margin: 6px 0;">GINA A. DELA CRUZ</div>
                <div style="width: 70%; margin: 6px auto 8px auto; border-top: 1pt solid #000; height: 0;"></div>
                <div style="font-size: 9pt; margin-top: 6px;">PROPERTY CUSTODIAN</div>
              </td>
              <td style="border: 0.5pt solid #000; padding: 12px 6px; text-align: center; width: 25%; vertical-align: top;">
                <div style="font-size: 10pt; margin-bottom: 8px;">Received by:</div>
                <div style="width: 70%; margin: 8px auto 6px auto; border-top: 1pt solid #000; height: 0;"></div>
                <div style="font-weight: bold; font-size: 11pt; margin: 6px 0;">${this.receivedByName().toUpperCase()}</div>
                <div style="width: 70%; margin: 6px auto 8px auto; border-top: 1pt solid #000; height: 0;"></div>
                <div style="font-size: 9pt; margin-top: 6px;">${this.receivedByOffice().toUpperCase()}</div>
              </td>
            </tr>
          </table>
        </div>
      `;

      const filename = `RIS-BATCH-${new Date().toISOString().split('T')[0]}.pdf`;
      const opt = {
        margin: 0,
        filename: filename,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
      };

      const iframe = document.createElement('iframe');
      iframe.style.position = 'fixed';
      iframe.style.right = '0';
      iframe.style.bottom = '0';
      iframe.style.width = '0';
      iframe.style.height = '0';
      iframe.style.border = '0';
      iframe.style.overflow = 'hidden';
      document.body.appendChild(iframe);

      const idoc = iframe.contentWindow?.document;
      if (!idoc) throw new Error('Unable to create print iframe');

      idoc.open();
      idoc.write(`<!doctype html><html><head><title>${filename}</title>
        <meta charset="utf-8" />
        <style>body{font-family: Arial, sans-serif; color:#000;}</style>
      </head><body>${pdfContent}</body></html>`);
      idoc.close();

      let printed = false;
      const printAndCleanup = () => {
        if (printed) return;
        printed = true;
        try {
          iframe.contentWindow?.focus();
          iframe.contentWindow?.print();
        } finally {
          setTimeout(() => { try { document.body.removeChild(iframe); } catch {} }, 500);
        }
      };

      iframe.onload = () => printAndCleanup();
      setTimeout(() => printAndCleanup(), 1000);
      setTimeout(() => { this.openModal(); }, 1500);
      
    } catch (error) {
      console.error('Error generating PDF:', error);
      alert('An error occurred while generating the PDF.');
    }
  }

  private getModalInstance() {
    if (!this.confirmModalElement) return null;
    const bootstrap = (window as any).bootstrap;
    if (bootstrap) {
      return bootstrap.Modal.getOrCreateInstance(this.confirmModalElement.nativeElement);
    }
    return null;
  }

  openModal() {
    this.getModalInstance()?.show();
  }

  closeModal() {
    this.getModalInstance()?.hide();
  }

  confirmPrinted() {
    this.isPrinted.set(true);
    this.closeModal();
  }
}

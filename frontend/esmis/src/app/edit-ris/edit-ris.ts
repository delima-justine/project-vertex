import { Component, ElementRef, inject, OnDestroy, OnInit, signal, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { SupplyService } from '../../services/supply.service';
import { SupplyRequest } from '../../models/smis.model';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AuthService } from '../../services/auth.service';
import { interval, Subscription } from 'rxjs';

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

  request = signal<SupplyRequest | null>(null);
  approver = signal(this.authService.currentUser());

  dateNow = new Date();
  today = new Date(this.dateNow.getFullYear(), this.dateNow.getMonth(), this.dateNow.getDate());

  @ViewChild('risPdf', { static: false }) risPdf?: ElementRef<HTMLElement>;
  
  // Real-time processing counter
  elapsedSeconds = signal(0);
  private timerSubscription?: Subscription;
  
  // For "Issue" quantity and remarks
  issueQty = signal<number>(0);
  remarks = signal<string>('');
  stockAvailable = signal<boolean>(true);

  // For "Received By"
  receivedByName = signal<string>('');
  receivedByOffice = signal<string>('');

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.loadRequest(Number(id));
    }

    // Start counting from 0 when the component loads
    this.timerSubscription = interval(1000).subscribe(() => {
      this.elapsedSeconds.update(s => s + 1);
    });
  }

  ngOnDestroy() {
    // Stop the timer
    this.timerSubscription?.unsubscribe();
  }

  loadRequest(id: number) {
    this.supplyService.getSupplyRequest(id).subscribe({
      next: (data) => {
        this.request.set(data);
        this.issueQty.set(data.quantity_req);
      },
      error: (err) => {
        console.error('Error fetching request', err);
        alert('Failed to load request data.');
        this.router.navigate(['/pending-requests']);
      }
    });
  }

  cancel() {
    this.router.navigate(['/pending-requests']);
  }

  saveAndApprove() {
    const req = this.request();
    const admin = this.approver();
    if (!req || !admin) return;

    if (confirm('Are you sure you want to approve this request?')) {
      this.supplyService.updateSupplyRequest(req.id, {
        status: 'approved',
        approved_by: admin.id,
        quantity_req: this.issueQty()
      }).subscribe({
        next: () => {
          alert('Request approved successfully!');
          this.router.navigate(['/pending-requests']);
        },
        error: (err) => {
          console.error('Error approving request', err);
          alert('Failed to approve request.');
        }
      });
    }
  }

  onStockAvailableChange(available: boolean) {
    this.stockAvailable.set(available);
    if (!available) {
      this.issueQty.set(0);
    } else {
      this.issueQty.set(this.request()?.quantity_req || 0);
    }
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
    const req = this.request();
    if (!req) {
      alert('Error: No request data found.');
      return;
    }

    try {
      const h2p = await import('html2pdf.js');
      const html2pdf = (h2p as any).default || h2p;

      // Extract data for the document
      const fullName = ((req.user?.first_name || '') + ' ' + (req.user?.last_name || '')).toUpperCase();
      const office = (req.user?.office?.office_name || '').toUpperCase();
      const stockYes = this.stockAvailable() ? '&#9679;' : '&#9675;'; // Filled vs Empty circle
      const stockNo = !this.stockAvailable() ? '&#9679;' : '&#9675;';

      // Build the document HTML strictly matching @ris-2026.pdf
      const pdfContent = `
        <div style="padding: 20px; font-family: Arial, sans-serif; color: #000; background: #fff; line-height: 1.2;">
          <!-- Header -->
          <div style="text-align: right; font-size: 11pt; margin-bottom: 30px;">
            <p style="margin: 0;">PUP-RISL-6-PSMO-010</p>
            <p style="margin: 0;">Rev. 2</p>
            <p style="margin: 0;">Effectivity Date: April 23, 2026</p>
            <p style="margin: 0;">Appendix 63</p>
          </div>

          <!-- Title -->
          <div style="text-align: center; font-size: 14pt; font-weight: bold; margin-bottom: 25px;">
            REQUISITION AND ISSUE SLIP
          </div>

          <!-- Main Table -->
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
              <tr>
                <td style="border: 0.5pt solid #000; padding: 8px;">${req.supply_id || ''}</td>
                <td style="border: 0.5pt solid #000; padding: 8px;">${req.supply?.unit?.unit_name || ''}</td>
                <td style="border: 0.5pt solid #000; padding: 8px;">${req.supply?.item_desc || ''}</td>
                <td style="border: 0.5pt solid #000; padding: 8px; text-align: center;">${req.quantity_req || 0}</td>
                <td style="border: 0.5pt solid #000; padding: 8px; text-align: center; font-size: 14pt;">${stockYes}</td>
                <td style="border: 0.5pt solid #000; padding: 8px; text-align: center; font-size: 14pt;">${stockNo}</td>
                <td style="border: 0.5pt solid #000; padding: 8px; text-align: center;">${this.issueQty() || ''}</td>
                <td style="border: 0.5pt solid #000; padding: 8px;">${this.remarks() || ''}</td>
              </tr>
              <!-- Empty row to match height if needed -->
              <tr style="height: 20px;">
                <td style="border: 0.5pt solid #000;"></td><td style="border: 0.5pt solid #000;"></td><td style="border: 0.5pt solid #000;"></td>
                <td style="border: 0.5pt solid #000;"></td><td style="border: 0.5pt solid #000;"></td><td style="border: 0.5pt solid #000;"></td>
                <td style="border: 0.5pt solid #000;"></td><td style="border: 0.5pt solid #000;"></td>
              </tr>
            </tbody>
          </table>

          <!-- Purpose -->
          <div style="margin-top: 15px; font-size: 11pt; border-bottom: 1px solid #000; padding-bottom: 5px;">
            Purpose: ${req.purpose || ''}
          </div>

          <!-- Signatures Table -->
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

      const filename = `RIS-${req.id}-${new Date().toISOString().split('T')[0]}.pdf`;
      const opt = {
        margin: 0,
        filename: filename,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
      };

      // Print using an off-screen iframe to show the browser print dialog
      // (avoids opening a new tab). Write the prepared HTML into the iframe
      // and call its print() method once loaded.
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

      // Some browsers fire onload on iframe when content is ready; fallback to a short timeout
      iframe.onload = () => printAndCleanup();
      setTimeout(() => printAndCleanup(), 1000);
      
    } catch (error) {
      console.error('Error generating PDF:', error);
      alert('An error occurred while generating the PDF.');
    }
  }
}

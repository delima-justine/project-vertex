import { Component, inject, OnDestroy, OnInit, signal } from '@angular/core';
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
  
  // Real-time processing counter
  elapsedSeconds = signal(0);
  private timerSubscription?: Subscription;
  
  // For "Issue" quantity and remarks
  issueQty = signal<number>(0);
  remarks = signal<string>('');
  stockAvailable = signal<boolean>(true);

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
}

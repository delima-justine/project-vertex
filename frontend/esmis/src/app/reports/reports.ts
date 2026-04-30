import { Component } from '@angular/core';
import { Sidebar } from "../sidebar/sidebar";

@Component({
  selector: 'app-reports',
  imports: [Sidebar],
  templateUrl: './reports.html',
  styleUrl: './reports.scss',
})
export class Reports {

  exportToExcel() {
    console.log('Exporting to Excel...');
  }

  openRequestLogs() {
    console.log('Opening Request Logs...');
  }

  openAdminAudit() {
    console.log('Opening Admin Audit...');
  }
}

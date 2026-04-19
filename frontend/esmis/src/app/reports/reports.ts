import { Component } from '@angular/core';
import { Sidebar } from "../sidebar/sidebar";
import { TopNav } from "../top-nav/top-nav";

@Component({
  selector: 'app-reports',
  imports: [Sidebar, TopNav],
  templateUrl: './reports.html',
  styleUrl: './reports.scss',
})
export class Reports {

  exportToExcel() {
    console.log('Exporting to Excel...');
  }
}

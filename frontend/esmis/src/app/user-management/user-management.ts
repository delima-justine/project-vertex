import { Component } from '@angular/core';
import { Sidebar } from "../sidebar/sidebar";

@Component({
  selector: 'app-user-management',
  imports: [Sidebar],
  templateUrl: './user-management.html',
  styleUrl: './user-management.scss',
})
export class UserManagement {

}

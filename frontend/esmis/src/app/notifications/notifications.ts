import { Component } from '@angular/core';
import { Sidebar } from "../sidebar/sidebar";
import { TopNav } from "../top-nav/top-nav";

@Component({
  selector: 'app-notifications',
  imports: [Sidebar, TopNav],
  templateUrl: './notifications.html',
  styleUrl: './notifications.scss',
})
export class Notifications {

}

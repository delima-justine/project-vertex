import { Component } from '@angular/core';
import { Sidebar } from "../sidebar/sidebar";
import { TopNav } from "../top-nav/top-nav";

@Component({
  selector: 'app-help-and-support',
  imports: [Sidebar, TopNav],
  templateUrl: './help-and-support.html',
  styleUrl: './help-and-support.scss',
})
export class HelpAndSupport {

}

import { Component, inject } from '@angular/core';
import { Sidebar } from "../sidebar/sidebar";
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-home',
  imports: [Sidebar],
  templateUrl: './home.html',
  styleUrl: './home.scss',
})
export class Home {
  authService = inject(AuthService);
  user = this.authService.currentUser;
}

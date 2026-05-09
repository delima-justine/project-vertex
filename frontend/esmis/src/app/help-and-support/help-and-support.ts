import { Component, inject } from '@angular/core';
import { Sidebar } from "../sidebar/sidebar";
import { TopNav } from "../top-nav/top-nav";
import { AuthService } from '../../services/auth.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-help-and-support',
  standalone: true,
  imports: [Sidebar, TopNav, CommonModule],
  templateUrl: './help-and-support.html',
  styleUrl: './help-and-support.scss',
})
export class HelpAndSupport {
  private authService = inject(AuthService);
  activeTab = 'general';

  get isAdmin(): boolean {
    const roleName = this.authService.currentUser()?.role?.role_name?.toLowerCase();
    return roleName === 'admin' || roleName === 'superadmin';
  }
}

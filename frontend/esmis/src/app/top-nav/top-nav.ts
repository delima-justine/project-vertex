import { Component, ElementRef, inject, signal, ViewChild } from '@angular/core';
import { AuthService } from '../../services/auth.service';
import { Router } from '@angular/router';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';

@Component({
  selector: 'app-top-nav',
  imports: [ReactiveFormsModule],
  templateUrl: './top-nav.html',
  styleUrl: './top-nav.scss',
})
export class TopNav {
  isOpen = signal(false);
  authService = inject(AuthService);
  user = this.authService.currentUser;
  router = inject(Router);
  
  private formBuilder = inject(FormBuilder);
  accountSettingsForm: FormGroup;

  @ViewChild('accountSettingsModal') accountSettingsModalElement!: ElementRef;

  toggleDropdown() {
    this.isOpen.set(!this.isOpen());
  }

  constructor() {
    this.accountSettingsForm = this.formBuilder.group({
      currentPassword: ['', [Validators.required]],
      newPassword: ['', [Validators.required, Validators.minLength(8)]],
      confirmNewPassword: ['', [Validators.required]],
    }, { validators: this.passwordMatchValidator });
  }

  private passwordMatchValidator(group: FormGroup) {
    const newPassword = group.get('newPassword')?.value;
    const confirmPassword = group.get('confirmNewPassword')?.value;
    
    if (newPassword !== confirmPassword) {
      group.get('confirmNewPassword')?.setErrors({ mismatch: true });
      return { mismatch: true };
    } else {
      // Clear errors if they were previously set by this validator
      const errors = group.get('confirmNewPassword')?.errors;
      if (errors) {
        delete errors['mismatch'];
        if (Object.keys(errors).length === 0) {
          group.get('confirmNewPassword')?.setErrors(null);
        } else {
          group.get('confirmNewPassword')?.setErrors(errors);
        }
      }
      return null;
    }
  }

  private getModalInstance() {
    const modalElement = this.accountSettingsModalElement.nativeElement;
    const bootstrap = (window as any).bootstrap;
    if (bootstrap) {
      return bootstrap.Modal.getOrCreateInstance(modalElement);
    }
    return null;
  }

  openAccountSettings() {
    this.isOpen.set(false); // Close dropdown
    this.getModalInstance()?.show();
  }

  closeAccountSettings() {
    this.getModalInstance()?.hide();
    this.accountSettingsForm.reset();
  }

  logout() {
    this.authService.logout().subscribe({
      next: () => {
        this.router.navigate(['/']);
      },
      error: (err) => {
        localStorage.removeItem('auth_token');
        this.authService.currentUser.set(null);
        this.router.navigate(['/']);
      }
    });
  }

  get currentPassword() {
    return this.accountSettingsForm.get('currentPassword');
  }

  get newPassword() {
    return this.accountSettingsForm.get('newPassword');
  }

  get confirmNewPassword() {
    return this.accountSettingsForm.get('confirmNewPassword');
  }
}

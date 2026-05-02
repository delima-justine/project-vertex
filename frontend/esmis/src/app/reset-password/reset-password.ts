import { Component, inject } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule } from '@angular/forms';

@Component({
  selector: 'app-reset-password',
  imports: [ReactiveFormsModule],
  templateUrl: './reset-password.html',
  styleUrl: './reset-password.scss',
})
export class ResetPassword {
  resetPasswordForm: FormGroup;
  formBuilder = inject(FormBuilder);

  constructor() {
    this.resetPasswordForm = this.formBuilder.group({
      newPassword: [''],
    });
  }

  resetPassword() {
    if (confirm('Are you sure you want to reset your password?')) {
      console.log('Password reset to:', this.resetPasswordForm.value.newPassword);
    } else {
      console.log('Password reset cancelled');
    }
  }

  get newPassword() {
    return this.resetPasswordForm.get('newPassword');
  }
}

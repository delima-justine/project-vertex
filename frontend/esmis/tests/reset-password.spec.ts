import { test, expect } from '@playwright/test';

test.describe('Reset Password Flow', () => {
  
  test.beforeEach(async ({ page }) => {
    // Clear localStorage
    await page.addInitScript(() => {
      window.localStorage.clear();
    });
  });

  test('AUTH-04: should allow resetting password with valid token', async ({ page }) => {
    const mockEmail = 'test@example.com';
    const mockToken = 'valid-reset-token';
    const expiryTime = new Date(Date.now() + 1000 * 60 * 10).toISOString(); // 10 minutes from now

    // 1. Mock Check Token API
    await page.route('**/api/check-reset-token', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          message: 'Token is valid.',
          expires_at: expiryTime,
          server_time: new Date().toISOString()
        })
      });
    });

    // 2. Mock Reset Password API
    await page.route('**/api/reset-password', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ message: 'Password has been reset successfully.' })
      });
    });

    // 3. Navigate to reset password page with query params
    await page.goto(`/reset-password?token=${mockToken}&email=${mockEmail}`);

    // 4. Verify initial state
    await expect(page.locator('.reset-pass-title')).toContainText('Reset Password');
    await expect(page.locator('.timer-value')).toBeVisible();

    // 5. Fill the form with a strong password (matching requirements)
    const newPass = 'StrongPass123!';
    await page.locator('input[formControlName="password"]').fill(newPass);
    await page.locator('input[formControlName="password_confirmation"]').fill(newPass);

    // 6. Click Reset Password button
    const resetBtn = page.locator('button.reset-btn');
    await expect(resetBtn).toBeEnabled();
    await resetBtn.click();

    // 7. Handle Confirmation Modal (Reset Password uses confirmService)
    const confirmBtn = page.getByRole('button', { name: 'Reset', exact: true });
    await expect(confirmBtn).toBeVisible();
    await confirmBtn.click();

    // 8. Verify success message
    await expect(page.locator('.alert-success')).toContainText('Password has been reset successfully');

    // 9. Verify eventual redirection to login
    await expect(page).toHaveURL(/\/login|\/$/, { timeout: 10000 });
  });

  test('should show error if reset link is invalid/expired', async ({ page }) => {
    // 1. Mock Check Token API Failure
    await page.route('**/api/check-reset-token', async route => {
      await route.fulfill({
        status: 400,
        contentType: 'application/json',
        body: JSON.stringify({ message: 'This reset link has expired.' })
      });
    });

    await page.goto('/reset-password?token=invalid&email=test@example.com');

    // 2. Verify error message
    await expect(page.locator('.alert-danger')).toContainText('This reset link has expired');
    
    // 3. Verify form is NOT visible
    await expect(page.locator('form')).not.toBeVisible();
    await expect(page.locator('button', { hasText: /Request New Link/i })).toBeVisible();
  });
});

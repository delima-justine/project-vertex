import { test, expect } from '@playwright/test';

test.describe('Authentication Flow', () => {
  
  test.beforeEach(async ({ page }) => {
    // Go to the base URL defined in playwright.config.ts
    await page.goto('/');
  });

  test('should display the login page correctly', async ({ page }) => {
    // Check page title
    await expect(page).toHaveTitle(/Login to SMIS/i);

    // Verify presence of the login form
    const loginForm = page.locator('form');
    await expect(loginForm).toBeVisible();

    // Verify email/office and password inputs using formControlName
    await expect(page.locator('input[formControlName="email"]')).toBeVisible();
    await expect(page.locator('input[formControlName="password"]')).toBeVisible();

    // Verify login button
    const loginButton = page.locator('button.login-btn');
    await expect(loginButton).toBeVisible();
  });

  test('should navigate to forgot password page', async ({ page }) => {
    // Find and click the forgot password button
    await page.locator('button', { hasText: /forgot password/i }).click();

    // Verify navigation to /forgot-pass
    await expect(page).toHaveURL(/\/forgot-pass/);
    await expect(page).toHaveTitle(/Forgot Password/i);
  });

  test('should show validation error on empty login', async ({ page }) => {
    // Select the password field
    const passwordInput = page.locator('input[formControlName="password"]');
    
    // "Touch" the field by focusing and blurring it to trigger Angular's touched state
    await passwordInput.focus();
    await passwordInput.blur();

    // Click login without entering credentials
    await page.locator('button.login-btn').click();

    // Check for the specific error message class used in authentication.html
    const passwordError = page.locator('.error-message', { hasText: /password is required/i });
    
    // Now the error should be visible
    await expect(passwordError).toBeVisible();
  });
});

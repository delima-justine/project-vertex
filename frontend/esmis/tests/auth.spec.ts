import { test, expect } from '@playwright/test';

test.describe('Authentication Flow', () => {
  
  test.beforeEach(async ({ page }) => {
    // Clear localStorage to ensure a clean state
    await page.addInitScript(() => {
      window.localStorage.clear();
    });
    await page.goto('/');
  });

  test('AUTH-01: should login successfully with valid credentials', async ({ page }) => {
    // 1. Mock Login API
    await page.route('**/api/login', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          token: 'mock-token',
          user: { id: 1, name: 'Test User', email: 'test@example.com', role: { role_name: 'User' } },
          permissions: ['view_create_request']
        })
      });
    });

    // 2. Fill login form
    await page.locator('input[formControlName="email"]').fill('test@example.com');
    await page.locator('input[formControlName="password"]').fill('password123');
    
    // 3. Submit
    await page.locator('button.login-btn').click();

    // 4. Verify redirection to home
    await expect(page).toHaveURL(/\/home/);
    
    // 5. Verify token in localStorage
    const token = await page.evaluate(() => window.localStorage.getItem('auth_token'));
    expect(token).toBe('mock-token');
  });

  test('AUTH-02: should show error message on invalid credentials', async ({ page }) => {
    // 1. Mock Login API Failure
    await page.route('**/api/login', async route => {
      await route.fulfill({
        status: 401,
        contentType: 'application/json',
        body: JSON.stringify({ message: 'Invalid email or password.' })
      });
    });

    // 2. Fill login form
    await page.locator('input[formControlName="email"]').fill('wrong@example.com');
    await page.locator('input[formControlName="password"]').fill('wrongpass');
    
    // 3. Submit
    await page.locator('button.login-btn').click();

    // 4. Verify error message is displayed
    const errorBox = page.locator('.error-message', { hasText: /Invalid email or password/i });
    await expect(errorBox).toBeVisible();
  });

  test('AUTH-03: should navigate to forgot password page', async ({ page }) => {
    await page.locator('button', { hasText: /forgot password/i }).click();
    await expect(page).toHaveURL(/\/forgot-pass/);
    await expect(page).toHaveTitle(/Forgot Password/i);
  });

  test('AUTH-05: should logout successfully', async ({ page }) => {
    // 1. Mock Authentication State
    await page.addInitScript(() => {
      window.localStorage.setItem('auth_token', 'mock-token');
    });

    // Mock Profile API
    await page.route('**/api/user/profile', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          user: { id: 1, first_name: 'Test', last_name: 'User', email: 'test@example.com', role: { role_name: 'User' } },
          permissions: []
        })
      });
    });

    // Mock Logout API
    await page.route('**/api/logout', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ message: 'Logged out successfully' })
      });
    });

    await page.goto('/home');

    // 2. Perform Logout Flow
    // a. Open User Dropdown in TopNav
    await page.locator('.dropdown-trigger').click();
    
    // b. Click Logout in dropdown
    await page.locator('.dropdown-item', { hasText: 'Logout' }).click();
    
    // c. Click Confirm Logout in modal
    const confirmButton = page.locator('.modal-footer .btn-danger', { hasText: 'LOGOUT' });
    await expect(confirmButton).toBeVisible();
    await confirmButton.click();

    // 3. Verify redirection to login
    await expect(page).toHaveURL(/\/login|\/$/);
    
    // 4. Verify token is removed
    const token = await page.evaluate(() => window.localStorage.getItem('auth_token'));
    expect(token).toBeNull();
  });

  test('AUTH-06: should redirect unauthenticated users to login', async ({ page }) => {
    // 1. Ensure no token
    await page.addInitScript(() => {
      window.localStorage.removeItem('auth_token');
    });

    // 2. Try to access a protected route
    await page.goto('/home');

    // 3. Verify redirection to root/login
    await expect(page).toHaveURL(/\/login|\/$/);
  });
});

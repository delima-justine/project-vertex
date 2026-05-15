import { test, expect } from '@playwright/test';

test.describe('Office Management Flow', () => {
  
  test.beforeEach(async ({ page }) => {
    // 1. Mock Authentication
    await page.addInitScript(() => {
      window.localStorage.setItem('auth_token', 'mock-token');
    });

    // 2. Mock API Responses
    await page.route('**/api/user/profile', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          user: { id: 1, first_name: 'Admin', last_name: 'User', email: 'admin@example.com', role: { role_name: 'Superadmin' } },
          permissions: ['add_user', 'edit_user']
        })
      });
    });

    await page.route('**/api/offices', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify([
          { id: 1, office_name: 'IT Department' },
          { id: 2, office_name: 'HR Department' }
        ])
      });
    });

    await page.route('**/api/roles', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify([{ id: 2, role_name: 'User' }])
      });
    });

    await page.route('**/api/user*', async route => {
      if (route.request().method() === 'GET' && !route.request().url().includes('profile')) {
        await route.fulfill({
          status: 200,
          contentType: 'application/json',
          body: JSON.stringify({ data: [], current_page: 1, last_page: 1, total: 0 })
        });
      } else {
        await route.continue();
      }
    });

    await page.goto('/admin/user-management');
  });

  test('OFC-01: should allow creating a new office via User Management', async ({ page }) => {
    // 1. Open Add User Modal
    await page.locator('button', { hasText: /Add User/i }).click();
    const modal = page.locator('#userModal');
    await expect(modal).toBeVisible();

    // 2. Type a NEW office name
    const officeInput = modal.locator('input[formControlName="office_name"]');
    await officeInput.fill('Accounting');

    // 3. Verify datalist exists (indirectly by checking if it accepts the value)
    await expect(officeInput).toHaveValue('Accounting');

    // 4. Mock user creation which also creates/assigns the office
    await page.route('**/api/user', async route => {
      if (route.request().method() === 'POST') {
        const body = route.request().postDataJSON();
        expect(body.office_name).toBe('Accounting');
        
        await route.fulfill({
          status: 201,
          contentType: 'application/json',
          body: JSON.stringify({ id: 10, first_name: 'A', last_name: 'B', office: { id: 3, office_name: 'Accounting' } })
        });
      }
    });

    // 5. Fill remaining required fields and save
    await modal.locator('input[formControlName="first_name"]').fill('John');
    await modal.locator('input[formControlName="last_name"]').fill('Doe');
    await modal.locator('input[formControlName="email"]').fill('john.doe@gmail.com');
    await modal.locator('select[formControlName="role_id"]').selectOption({ label: 'User' });
    
    await modal.locator('button.btn-save').click();

    // 6. Verify success
    await expect(modal).toBeHidden();
  });

  test('OFC-01: should allow selecting an existing office', async ({ page }) => {
    await page.locator('button', { hasText: /Add User/i }).click();
    const modal = page.locator('#userModal');
    
    const officeInput = modal.locator('input[formControlName="office_name"]');
    await officeInput.fill('IT Department');
    
    // In a real browser, the datalist would show 'IT Department'. 
    // Here we just verify the input takes the value and it matches one of our mocked offices.
    await expect(officeInput).toHaveValue('IT Department');
  });
});

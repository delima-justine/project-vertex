import { test, expect } from '@playwright/test';

test.describe('Supply & Inventory Management Flow', () => {
  
  test.beforeEach(async ({ page }) => {
    // 1. Mock Authentication
    await page.addInitScript(() => {
      window.localStorage.setItem('auth_token', 'mock-token');
    });

    // 2. Mock API Responses
    // Profile with admin permissions
    await page.route('**/api/user/profile', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          user: { id: 1, first_name: 'Admin', last_name: 'User', email: 'admin@example.com', role: { role_name: 'Superadmin' } },
          permissions: ['add_supply', 'edit_supply', 'delete_supply', 'add_category', 'delete_category', 'add_unit', 'delete_unit']
        })
      });
    });

    // Mock Supplies
    await page.route('**/api/supplies', async route => {
      if (route.request().method() === 'GET') {
        await route.fulfill({
          status: 200,
          contentType: 'application/json',
          body: JSON.stringify([
            { stock_num: 'SN-001', item_desc: 'Paper A4', quantity: 100, status: 'Available', category_id: 1, category: { category_name: 'Office Supplies' }, unit_id: 1, unit: { unit_name: 'Ream' } }
          ])
        });
      } else {
        await route.continue();
      }
    });

    // Mock Categories
    await page.route('**/api/categories', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify([
          { id: 1, category_name: 'Office Supplies' }
        ])
      });
    });

    // Mock Units
    await page.route('**/api/units', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify([
          { id: 1, unit_name: 'Ream' }
        ])
      });
    });

    // Mock Requests (for dashboard summary)
    await page.route('**/api/supply-requests*', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ data: [], total: 0 })
      });
    });

    await page.goto('/home');
  });

  test('SUP-01: should allow adding a new supply item', async ({ page }) => {
    // 1. Open the "Add Supply" modal
    await page.locator('button', { hasText: /Add Supply/i }).click();
    const modal = page.locator('#supplyModal');
    await expect(modal).toBeVisible();

    // 2. Fill the form
    await modal.locator('input[formControlName="stock_num"]').fill('SN-999');
    await modal.locator('textarea[formControlName="item_desc"]').fill('Test Supply Description');
    await modal.locator('select[formControlName="category_id"]').selectOption({ label: 'Office Supplies' });
    await modal.locator('select[formControlName="unit_id"]').selectOption({ label: 'Ream' });
    await modal.locator('input[formControlName="quantity"]').fill('50');

    // 3. Mock Store Success
    await page.route('**/api/supplies', async route => {
      if (route.request().method() === 'POST') {
        await route.fulfill({
          status: 201,
          contentType: 'application/json',
          body: JSON.stringify({ message: 'Supply added successfully!' })
        });
      }
    });

    // 4. Save
    await modal.locator('button.btn-save').click();

    // 5. Verify modal closed
    await expect(modal).not.toBeVisible();
  });

  test('SUP-02: should allow updating stock quantity', async ({ page }) => {
    // 1. Click Edit on an existing supply (using the pencil icon)
    await page.locator('tr', { hasText: 'SN-001' }).locator('.bi-pencil-square').locator('xpath=..').click();
    const modal = page.locator('#supplyModal');
    await expect(modal).toBeVisible();

    // 2. Update quantity
    const quantityInput = modal.locator('input[formControlName="quantity"]');
    await expect(quantityInput).toHaveValue('100');
    await quantityInput.fill('150');

    // 3. Mock Update Success
    await page.route('**/api/supplies/SN-001', async route => {
      if (route.request().method() === 'PATCH') {
        await route.fulfill({
          status: 200,
          contentType: 'application/json',
          body: JSON.stringify({ message: 'Supply updated successfully!' })
        });
      }
    });

    // 4. Save
    await modal.locator('button.btn-save').click();

    // 5. Verify modal closed
    await expect(modal).not.toBeVisible();
  });

  test('SUP-03: Supply Categories CRUD', async ({ page }) => {
    // 1. Open Add Supply modal then Category modal
    await page.locator('button', { hasText: /Add Supply/i }).click();
    await page.locator('#supplyModal .bi-plus-lg').first().locator('xpath=..').click();
    
    const catModal = page.locator('#categoryModal');
    await expect(catModal).toBeVisible();

    // 2. Add Category
    await catModal.locator('input[placeholder*="category name"]').fill('New Test Category');
    
    await page.route('**/api/categories', async route => {
      if (route.request().method() === 'POST') {
        await route.fulfill({
          status: 201,
          contentType: 'application/json',
          body: JSON.stringify({ id: 2, category_name: 'New Test Category' })
        });
      }
    });

    await catModal.locator('button', { hasText: /ADD/i }).click();

    // 3. Delete Category
    await catModal.locator('.list-group-item', { hasText: 'Office Supplies' }).locator('.bi-trash').locator('xpath=..').click();
    
    const confirmModal = page.locator('.modal-dialog', { hasText: 'Delete Category' });
    await expect(confirmModal).toBeVisible();

    await page.route('**/api/categories/1', async route => {
      if (route.request().method() === 'DELETE') {
        await route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify({ message: 'Deleted' }) });
      }
    });

    await confirmModal.locator('button', { hasText: /Delete/i }).click();
    await expect(confirmModal).not.toBeVisible();
  });

  test('SUP-04: Supply Units CRUD', async ({ page }) => {
    // 1. Open Add Supply modal then Unit modal
    await page.locator('button', { hasText: /Add Supply/i }).click();
    // Second plus icon is for Units
    await page.locator('#supplyModal .bi-plus-lg').nth(1).locator('xpath=..').click();
    
    const unitModal = page.locator('#unitModal');
    await expect(unitModal).toBeVisible();

    // 2. Add Unit
    await unitModal.locator('input[placeholder*="unit name"]').fill('New Unit');
    
    await page.route('**/api/units', async route => {
      if (route.request().method() === 'POST') {
        await route.fulfill({
          status: 201,
          contentType: 'application/json',
          body: JSON.stringify({ id: 2, unit_name: 'New Unit' })
        });
      }
    });

    await unitModal.locator('button', { hasText: /ADD/i }).click();

    // 3. Delete Unit
    await unitModal.locator('.list-group-item', { hasText: 'Ream' }).locator('.bi-trash').locator('xpath=..').click();
    
    const confirmModal = page.locator('.modal-dialog', { hasText: 'Delete Unit' });
    await expect(confirmModal).toBeVisible();

    await page.route('**/api/units/1', async route => {
      if (route.request().method() === 'DELETE') {
        await route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify({ message: 'Deleted' }) });
      }
    });

    await confirmModal.locator('button', { hasText: /Delete/i }).click();
    await expect(confirmModal).not.toBeVisible();
  });

  test('SUP-05: should allow soft deleting a supply', async ({ page }) => {
    // 1. Click Delete (trash icon)
    await page.locator('tr', { hasText: 'SN-001' }).locator('.bi-trash').locator('xpath=..').click();

    // 2. Verify Confirmation Modal
    const confirmModal = page.locator('.modal-dialog', { hasText: 'Delete Supply' });
    await expect(confirmModal).toBeVisible();

    // 3. Mock Delete Success
    await page.route('**/api/supplies/SN-001', async route => {
      if (route.request().method() === 'DELETE') {
        await route.fulfill({
          status: 200,
          contentType: 'application/json',
          body: JSON.stringify({ message: 'Supply deleted successfully' })
        });
      }
    });

    // 4. Confirm Delete
    await confirmModal.locator('button', { hasText: /Delete/i }).click();

    // 5. Verify modal closed
    await expect(confirmModal).not.toBeVisible();
  });
});

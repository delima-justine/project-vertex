import { test, expect } from '@playwright/test';

test.describe('Create Request Flow', () => {
  
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
          user: { id: 1, name: 'Test User', email: 'test@example.com', office: 'IT', role: { role_name: 'User' } },
          permissions: ['view_create_request']
        })
      });
    });

    await page.route('**/api/supplies', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify([
          { stock_num: 'SN-001', item_desc: 'Paper A4', status: 'Available', quantity: 100, category_id: 1, category: { category_name: 'Office Supplies' }, unit: { unit_name: 'Ream' } },
          { stock_num: 'SN-002', item_desc: 'Ballpen Black', status: 'Available', quantity: 50, category_id: 1, category: { category_name: 'Office Supplies' }, unit: { unit_name: 'Piece' } },
          { stock_num: 'SN-003', item_desc: 'Ink Cartridge', status: 'Out of Stock', quantity: 0, category_id: 2, category: { category_name: 'Printer Supplies' }, unit: { unit_name: 'Cartridge' } }
        ])
      });
    });

    await page.route('**/api/categories', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify([
          { id: 1, category_name: 'Office Supplies' },
          { id: 2, category_name: 'Printer Supplies' }
        ])
      });
    });

    await page.goto('/create-request');
  });

  test('should allow user to create a supply request', async ({ page }) => {
    // Verify we are on the correct page
    await expect(page.getByText('Create New Request')).toBeVisible();

    // 1. Open the "Add Items" modal
    await page.locator('button.btn-add-items').click();
    const modal = page.locator('#supplyInventoryModal');
    await expect(modal).toBeVisible();

    // 2. Search for an item in the modal
    const searchInput = modal.locator('input[placeholder*="Stock number"]');
    await searchInput.fill('Paper');
    await expect(modal.locator('td', { hasText: 'Paper A4' })).toBeVisible();
    // Wait for the other item to be hidden to handle debounce/timing
    await expect(modal.locator('td', { hasText: 'Ballpen Black' })).toBeHidden();

    // Clear search to see all items again
    await searchInput.fill('');
    await expect(modal.locator('td', { hasText: 'Ballpen Black' })).toBeVisible();

    // 3. Select an item
    // Note: Out of stock items should be disabled
    await expect(modal.locator('tr', { hasText: 'Ink Cartridge' }).locator('input[type="checkbox"]')).toBeDisabled();
    
    await modal.locator('tr', { hasText: 'Paper A4' }).locator('input[type="checkbox"]').check();
    
    // Clear search to see other items
    await searchInput.fill('');
    await modal.locator('tr', { hasText: 'Ballpen Black' }).locator('button', { hasText: /Add/i }).click();

    // 4. Confirm selection
    await modal.locator('button.btn-save').click();
    await expect(modal).not.toBeVisible();

    // 5. Verify items are in the request list table
    const requestTable = page.locator('.request-list-container table');
    await expect(requestTable.locator('tr', { hasText: 'Paper A4' })).toBeVisible();
    await expect(requestTable.locator('tr', { hasText: 'Ballpen Black' })).toBeVisible();

    // 6. Adjust quantity
    const paperRow = requestTable.locator('tr', { hasText: 'Paper A4' });
    const quantityInput = paperRow.locator('input.quantity-input');
    await quantityInput.fill('5');

    // 7. Enter purpose
    const purposeTextarea = page.locator('textarea[placeholder*="purpose"]');
    await purposeTextarea.fill('Monthly office supply replenishment.');

    // 8. Mock submission success
    await page.route('**/api/supply-requests/batch-store', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ message: 'Request submitted successfully!' })
      });
    });

    // 9. Submit the request
    const submitButton = page.locator('button.btn-submit');
    await expect(submitButton).toBeEnabled();
    await submitButton.click();

    // 10. Verify post-submission state
    // Wait for the empty message to appear in the table
    await expect(page.locator('.request-list-container table tbody')).toContainText('No items added to request list.');
    await expect(purposeTextarea).toHaveValue('');
  });

  test('should disable submit button if requirements are not met', async ({ page }) => {
    const submitButton = page.locator('button.btn-submit');
    
    // Initial state: no items, no purpose
    await expect(submitButton).toBeDisabled();

    // Add items but no purpose
    await page.locator('button.btn-add-items').click();
    // Use tbody to avoid matching the header checkbox
    await page.locator('#supplyInventoryModal tbody tr').first().locator('input[type="checkbox"]').check();
    await page.locator('#supplyInventoryModal button.btn-save').click();
    
    await expect(submitButton).toBeDisabled();

    // Add purpose but remove items
    await page.locator('textarea[placeholder*="purpose"]').fill('Some purpose');
    await page.locator('.request-list-container table button.btn-danger').click(); // Remove item
    
    await expect(submitButton).toBeDisabled();
  });
});

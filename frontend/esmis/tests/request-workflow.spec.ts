import { test, expect } from '@playwright/test';

test.describe('Supply Request Workflow Flow', () => {
  
  test.use({ viewport: { width: 1280, height: 720 } });

  test.beforeEach(async ({ page }) => {
    await page.addInitScript(() => {
      window.localStorage.setItem('auth_token', 'mock-token');
      window.print = () => {}; 
    });

    await page.route('**/api/user/profile', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          user: { id: 1, first_name: 'Admin', last_name: 'User', email: 'admin@example.com', role: { role_name: 'Superadmin' } },
          permissions: ['view_pending_requests', 'view_approved_requests', 'edit_ris', 'deny_request']
        })
      });
    });

    await page.route('**/api/supply-requests/status-counts', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ pending: 1, approved: 1, released: 0, disapproved: 0 })
      });
    });
  });

  test('REQ-03: should allow disapproving a pending request batch', async ({ page }) => {
    await page.route('**/api/supply-requests?status=pending*', async route => {
      await route.fulfill({
        status: 200,
        body: JSON.stringify({
          data: [{ id: 1, batch_id: 'B1', quantity_req: 10, created_at: new Date().toISOString(), user: { first_name: 'Test', last_name: 'User', office: { office_name: 'IT' } }, supply: { item_desc: 'P1' } }],
          current_page: 1, last_page: 1, total: 1
        })
      });
    });

    await page.route('**/api/supply-requests/batch-store', async route => {
      await route.fulfill({ status: 200, body: JSON.stringify({ message: 'Success' }) });
    });

    await page.goto('/pending-requests');
    
    // Open Detail Modal
    await page.locator('.desktop-view tr', { hasText: 'Test User' }).locator('button.btn-warning').click();
    const detailModal = page.locator('#requestDetailsModal');
    await expect(detailModal).toBeVisible();

    // Click Disapprove inside Detail Modal (using danger button)
    await detailModal.locator('button.btn-danger', { hasText: /DISAPPROVE BATCH/i }).click();
    
    // Confirm in GLOBAL confirmation modal
    const confirmBtn = page.locator('app-confirm-modal .modal.show button.btn-save');
    await expect(confirmBtn).toBeVisible();
    await confirmBtn.click();

    // Verify detail modal hides
    await expect(detailModal).toBeHidden({ timeout: 10000 });
  });

  test('REQ-06 & REQ-02: should allow editing RIS and approving requests', async ({ page }) => {
    await page.route('**/api/supply-requests?status=pending*', async route => {
        await route.fulfill({
          status: 200,
          body: JSON.stringify({
            data: [{ id: 1, batch_id: 'B2', quantity_req: 10, created_at: new Date().toISOString(), user: { first_name: 'John', last_name: 'Doe', office: { office_name: 'HR' } }, supply: { item_desc: 'B1' } }],
            current_page: 1, last_page: 1, total: 1
          })
        });
      });

    await page.route('**/api/supply-requests/1', async route => {
      await route.fulfill({
        status: 200,
        body: JSON.stringify({ id: 1, batch_id: 'B2', quantity_req: 10, user: { first_name: 'John', last_name: 'Doe', office: { office_name: 'HR' } }, supply: { item_desc: 'B1', unit: { unit_name: 'P' } } })
      });
    });

    await page.route('**/api/supply-requests/batch-store', async route => {
      await route.fulfill({ status: 200, body: JSON.stringify({ message: 'Success' }) });
    });

    await page.goto('/pending-requests');
    
    // Open Edit RIS via Detail Modal
    await page.locator('.desktop-view tr', { hasText: 'John Doe' }).locator('button.btn-warning').click();
    await page.locator('#requestDetailsModal.show button.btn-primary', { hasText: /EDIT BATCH RIS/i }).click();

    // Edit and Print
    await expect(page).toHaveURL(/\/requests\/edit-ris\/1/);
    await page.locator('input[type="number"]').first().fill('8');
    await page.locator('button.print-btn').click();
    
    // Confirm Print Status
    await page.locator('.modal.show button.btn-primary', { hasText: /Yes, I have/i }).click();

    // Save & Approve
    await page.locator('button.btn-success', { hasText: /Save & Approve Batch/i }).click();
    await page.locator('app-confirm-modal .modal.show button.btn-save').click();

    // Verify Redirection
    await expect(page).toHaveURL(/\/pending-requests/, { timeout: 10000 });
  });

  test('REQ-04: should allow releasing an approved request', async ({ page }) => {
    await page.route('**/api/supply-requests?status=approved*', async route => {
      await route.fulfill({
        status: 200,
        body: JSON.stringify({
          data: [{ id: 2, batch_id: 'B3', quantity_req: 5, created_at: new Date().toISOString(), user: { first_name: 'Jane', last_name: 'Smith', office: { office_name: 'IT' } }, supply: { item_desc: 'I1' }, approver: { first_name: 'A', last_name: 'U' } }],
          current_page: 1, last_page: 1, total: 1
        })
      });
    });

    await page.route('**/api/supply-requests/batch-store', async route => {
      await route.fulfill({ status: 200, body: JSON.stringify({ message: 'Success' }) });
    });

    await page.goto('/approved-requests');
    
    // Click Release
    await page.locator('.desktop-view tr', { hasText: 'Jane Smith' }).locator('button.btn-info').click();
    
    // Confirm Release
    const releaseBtn = page.locator('app-confirm-modal .modal.show button.btn-save');
    await releaseBtn.click();

    // Verify hidden
    await expect(releaseBtn).toBeHidden({ timeout: 10000 });
  });
});

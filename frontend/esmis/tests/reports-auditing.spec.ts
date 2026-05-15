import { test, expect } from '@playwright/test';

test.describe('Reports & Auditing Flow', () => {
  
  test.beforeEach(async ({ page }) => {
    // 1. Mock Authentication as Superadmin
    await page.addInitScript(() => {
      window.localStorage.setItem('auth_token', 'mock-token');
    });

    // 2. Mock API Responses
    await page.route('**/api/user/profile', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          user: { id: 1, first_name: 'Super', last_name: 'Admin', email: 'super@example.com', role: { role_name: 'Superadmin' } },
          permissions: ['view_reports', 'view_admin_audit', 'view_archive', 'restore_archive']
        })
      });
    });

    // Mock Offices
    await page.route('**/api/offices', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify([{ id: 1, office_name: 'IT' }])
      });
    });

    // Mock Admins list
    await page.route('**/api/roles', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify([
          { id: 1, role_name: 'Superadmin', users: [{ id: 1, first_name: 'Super', last_name: 'Admin' }] }
        ])
      });
    });

    // Mock Supply Requests (Logs)
    await page.route('**/api/supply-requests*', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: [
            { id: 1, batch_id: 'B1', quantity_req: 10, status: 'pending', created_at: new Date().toISOString(), user: { first_name: 'John', last_name: 'Doe', office: { office_name: 'IT' } }, supply: { item_desc: 'Paper' } }
          ],
          total: 1
        })
      });
    });

    await page.goto('/reports');
  });

  test('AUD-01: should display admin audit logs', async ({ page }) => {
    // 1. Switch to Admin Audit view
    await page.locator('button', { hasText: /Admin Audit/i }).click();

    // 2. Mock Audit Logs response
    await page.route('**/api/admin-audits*', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: [
            { id: 1, admin_name: 'Super Admin', action_type: 'UPDATE', description: 'Updated supply quantity', performed_at: new Date().toISOString(), ip_address: '127.0.0.1' }
          ],
          current_page: 1, last_page: 1, total: 1
        })
      });
    });

    // Trigger filter to load data if not automatic
    await page.locator('button', { hasText: /Apply Filters/i }).click();

    // 3. Verify audit record is visible
    await expect(page.locator('table.report-table')).toContainText('Super Admin');
    await expect(page.locator('table.report-table')).toContainText('UPDATE');
    await expect(page.locator('table.report-table')).toContainText('Updated supply quantity');
  });

  test('AUD-02: should allow viewing archived records', async ({ page }) => {
    // 1. Mock Archive API
    await page.route('**/api/archive*', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify([
          { id: 1, table_name: 'tbl_request', original_id: 101, data: { status: 'approved', quantity_req: 5, supply: { item_desc: 'Old Paper' } }, archiver: { first_name: 'Super', last_name: 'Admin' }, archived_at: new Date().toISOString() }
        ])
      });
    });

    // 2. Open Archive Modal
    await page.locator('button', { hasText: /Open Archive/i }).click();
    const modal = page.locator('#archiveHistoryModal');
    await expect(modal).toBeVisible();

    // 3. Verify archived record is visible
    await expect(modal.locator('table.archive-table')).toContainText('Old Paper');
    await expect(modal.locator('table.archive-table')).toContainText('Super Admin');
  });

  test('REP-01 & REP-02: should handle report filtering and export interaction', async ({ page }) => {
    // 1. Change Status filter
    await page.locator('select#statusSelect').selectOption('pending');
    
    // 2. Click Apply Filters
    await page.locator('button', { hasText: /Apply Filters/i }).click();

    // 3. Verify Export button exists (we can't easily test Excel generation in E2E, but we check if it triggers)
    const exportBtn = page.locator('button.export-btn');
    await expect(exportBtn).toBeVisible();
    await exportBtn.click();
    
    // Since exportToExcel is an async method that uses ExcelJS, 
    // we just verify it doesn't crash the UI.
    await expect(page.locator('.header-panel')).toContainText('Reports');
  });
});

import { test, expect } from '@playwright/test';

test.describe('Responsive Layout Flow', () => {
  
  test.beforeEach(async ({ page }) => {
    // Mock Authentication
    await page.addInitScript(() => {
      window.localStorage.setItem('auth_token', 'mock-token');
    });

    // Mock Profile
    await page.route('**/api/user/profile', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          user: { id: 1, first_name: 'Admin', last_name: 'User', email: 'admin@example.com', role: { role_name: 'Superadmin' } },
          permissions: ['add_user', 'edit_user', 'delete_user']
        })
      });
    });

    // Mock Users
    await page.route('**/api/user*', async route => {
      if (route.request().method() === 'GET' && !route.request().url().includes('profile')) {
        await route.fulfill({
          status: 200,
          contentType: 'application/json',
          body: JSON.stringify({
            data: [{ id: 1, first_name: 'Test', last_name: 'User', email: 'test@example.com', office: { office_name: 'IT' }, role: { role_name: 'User' } }],
            current_page: 1, last_page: 1, total: 1
          })
        });
      } else {
        await route.continue();
      }
    });

    // Mock Offices/Roles
    await page.route('**/api/offices', async route => {
      await route.fulfill({ status: 200, body: JSON.stringify([]) });
    });
    await page.route('**/api/roles', async route => {
      await route.fulfill({ status: 200, body: JSON.stringify([]) });
    });
  });

  test('Desktop View (1280x720): Sidebar and Desktop Table should be visible', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 720 });
    await page.goto('/admin/user-management');

    // 1. Sidebar should be visible and NOT transformed away
    const sidebar = page.locator('.sidebar');
    await expect(sidebar).toBeVisible();
    
    // Check transform (should be 'none' or matrix with 0 translation)
    await expect(sidebar).toHaveCSS('transform', 'none');

    // 2. User Management Desktop Table should be visible
    await expect(page.locator('.desktop-view')).toBeVisible();
    await expect(page.locator('.mobile-view')).not.toBeVisible();
  });

  test('Tablet View (1024x768): Sidebar should be off-canvas', async ({ page }) => {
    await page.setViewportSize({ width: 1024, height: 768 });
    await page.goto('/admin/user-management');

    // 1. Sidebar should be hidden by transform: translateX(-100%)
    const sidebar = page.locator('.sidebar');
    // transform: matrix(1, 0, 0, 1, -280, 0) is translateX(-280px)
    await expect(sidebar).toHaveCSS('transform', /matrix\(1, 0, 0, 1, -280, 0\)/);
  });

  test('Mobile View (375x667): Mobile template and Bottom Nav should be visible', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 667 });
    await page.goto('/admin/user-management');

    // 1. Desktop View should be hidden
    await expect(page.locator('.desktop-view')).not.toBeVisible();

    // 2. Mobile View (cards) should be visible
    await expect(page.locator('.mobile-view')).toBeVisible();

    // 3. Mobile Bottom Nav should be visible (it's inside app-mobile-nav)
    // Note: It might be considered "hidden" if it's off-screen or has 0 height, 
    // but the SCSS shows it at bottom: 0 with height: 64px.
    const bottomNav = page.locator('.mobile-bottom-nav');
    await expect(bottomNav).toBeVisible();
  });
});

import { test, expect } from '@playwright/test';

test.describe('Notifications Flow', () => {
  
  test.beforeEach(async ({ page }) => {
    // 1. Mock Authentication
    await page.addInitScript(() => {
      window.localStorage.setItem('auth_token', 'mock-token');
    });

    // 2. Mock Profile
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

    // 3. Mock Unread Count
    await page.route('**/api/notifications/unread-count', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ count: 1 })
      });
    });

    // 4. Mock Offices (needed for filters)
    await page.route('**/api/offices', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify([{ id: 1, office_name: 'IT' }])
      });
    });

    // 5. Mock Notifications List
    await page.route('**/api/notifications*', async route => {
      if (route.request().method() === 'GET') {
        await route.fulfill({
          status: 200,
          contentType: 'application/json',
          body: JSON.stringify({
            data: [
              { id: 1, message: 'Your request has been approved', action: 'approved', read_at: null, created_at: new Date().toISOString() }
            ],
            current_page: 1,
            last_page: 1,
            total: 1
          })
        });
      } else {
        await route.continue();
      }
    });

    await page.goto('/notifications');
  });

  test('NOTI-01: should display notifications correctly', async ({ page }) => {
    // Verify notification is visible
    const notification = page.locator('.notification-item', { hasText: 'Your request has been approved' });
    await expect(notification).toBeVisible();
    
    // Check unread state
    await expect(notification).toHaveClass(/unread/);
    await expect(notification.locator('.badge', { hasText: 'New' })).toBeVisible();
  });

  test('NOTI-02: should mark notification as read on click', async ({ page }) => {
    const notification = page.locator('.notification-item', { hasText: 'Your request has been approved' });
    
    // Mock Mark as Read API
    await page.route('**/api/notifications/1/read', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ message: 'Marked as read' })
      });
    });

    // Click notification
    await notification.click();

    // Verify unread state is removed
    await expect(notification).not.toHaveClass(/unread/);
    await expect(notification.locator('.badge', { hasText: 'New' })).not.toBeVisible();
  });

  test('NOTI-02 (Bulk): should mark all as read', async ({ page }) => {
    // Mock Mark All Read API
    await page.route('**/api/notifications/mark-all-read', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ message: 'All marked as read' })
      });
    });

    // Click Mark all as read
    await page.locator('button', { hasText: /Mark all as read/i }).click();

    // Verify notification is no longer unread
    const notification = page.locator('.notification-item');
    await expect(notification).not.toHaveClass(/unread/);
  });

  test('should delete a notification', async ({ page }) => {
    // Mock Delete API
    await page.route('**/api/notifications/1', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ message: 'Deleted' })
      });
    });

    // Open dropdown
    await page.locator('.notification-item').locator('button[data-bs-toggle="dropdown"]').click();
    
    // Click Delete
    await page.locator('.dropdown-item', { hasText: /Delete/i }).click();

    // Verify empty state
    await expect(page.locator('.notification-item')).toHaveCount(0);
    await expect(page.locator('.empty-state')).toBeVisible();
    await expect(page.locator('text=No notifications yet.')).toBeVisible();
  });

  test('should filter notifications by tab', async ({ page }) => {
    // Click 'Approved' tab
    await page.locator('.nav-link', { hasText: 'Approved' }).click();

    // Verify loader or list update (mock is same, but we check the tab state)
    // Target the specific tab button, not the sidebar link
    await expect(page.locator('.tab-container .nav-link.active')).toHaveText(/Approved/i);
  });
});

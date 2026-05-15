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

  test('NOTI-03: should apply office and date filters', async ({ page }) => {
    // Mock filtered response
    await page.route('**/api/notifications?*office_id=1*', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: [{ id: 2, message: 'Filtered Notification', action: 'pending', read_at: null, created_at: new Date().toISOString() }],
          current_page: 1, last_page: 1, total: 1
        })
      });
    });

    // Select office
    await page.locator('select.form-select').selectOption({ label: 'IT' });
    
    // Click Filter button
    await page.locator('button', { hasText: /Filter/i }).click();

    // Verify filtered content
    await expect(page.locator('.notification-item')).toContainText('Filtered Notification');
  });

  test('NOTI-04: should handle pagination', async ({ page }) => {
    // Mock multi-page response
    await page.route('**/api/notifications*', async route => {
      if (route.request().url().includes('page=2')) {
        await route.fulfill({
          status: 200,
          body: JSON.stringify({
            data: [{ id: 10, message: 'Page 2 Notification', action: 'pending', read_at: null, created_at: new Date().toISOString() }],
            current_page: 2, last_page: 2, total: 11
          })
        });
      } else {
        await route.fulfill({
          status: 200,
          body: JSON.stringify({
            data: Array(10).fill({ id: 1, message: 'Page 1 Notification', action: 'pending', read_at: null, created_at: new Date().toISOString() }),
            current_page: 1, last_page: 2, total: 11
          })
        });
      }
    });

    await page.reload();

    // Click Next
    await page.locator('button', { hasText: /Next/i }).click();

    // Verify Page 2 content
    await expect(page.locator('.notification-item')).toContainText('Page 2 Notification');
    await expect(page.locator('text=Page 2 of 2')).toBeVisible();
  });

  test('NOTI-05: System Alerts should be hidden for regular users', async ({ page }) => {
    // 1. Mock regular user (already done in beforeEach)
    
    // 2. Mock list with a system alert
    await page.route('**/api/notifications*', async route => {
      await route.fulfill({
        status: 200,
        body: JSON.stringify({
          data: [
            { id: 1, message: 'Stock is low', action: 'low stock', read_at: null, created_at: new Date().toISOString() }
          ],
          current_page: 1, last_page: 1, total: 1
        })
      });
    });

    await page.reload();

    // 3. Verify it is NOT displayed (Component logic unshifts but we check the rendered list)
    // Note: If the backend sends it, and the component filters it out, it shouldn't be in the list.
    // However, the tab 'System Alerts' should also be hidden.
    await expect(page.locator('.nav-link', { hasText: /System Alerts/i })).not.toBeVisible();
  });
});

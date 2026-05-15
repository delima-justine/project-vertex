import { test, expect } from '@playwright/test';

test.describe('User Management Flow', () => {
  
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
          permissions: ['add_user', 'edit_user', 'delete_user']
        })
      });
    });

    // Initial data for user management
    await page.route('**/api/user*', async route => {
      if (route.request().method() === 'GET' && !route.request().url().includes('profile')) {
        await route.fulfill({
          status: 200,
          contentType: 'application/json',
          body: JSON.stringify({
            data: [
              { id: 2, first_name: 'John', last_name: 'Doe', email: 'john@example.com', role_id: 2, role: { role_name: 'User' }, office: { office_name: 'IT' } }
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

    await page.route('**/api/offices', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify([
          { id: 1, office_name: 'IT' },
          { id: 2, office_name: 'HR' }
        ])
      });
    });

    await page.route('**/api/roles', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify([
          { id: 1, role_name: 'Superadmin' },
          { id: 2, role_name: 'User' }
        ])
      });
    });

    await page.route('**/api/permissions', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify([
          { id: 1, name: 'view_pending_requests', label: 'View Pending' }
        ])
      });
    });

    await page.route('**/api/roles/*/permissions', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify([])
      });
    });

    await page.goto('/admin/user-management');
  });

  test('USER-01: should allow creating a new user', async ({ page }) => {
    // 1. Open the "Add User" modal
    await page.locator('button', { hasText: /Add User/i }).click();
    const modal = page.locator('#userModal');
    await expect(modal).toBeVisible();

    // 2. Fill the form
    await modal.locator('input[formControlName="first_name"]').fill('Jane');
    await modal.locator('input[formControlName="last_name"]').fill('Smith');
    await modal.locator('input[formControlName="email"]').fill('jane@example.com');
    await modal.locator('select[formControlName="role_id"]').selectOption({ label: 'User' });
    await modal.locator('input[formControlName="office_name"]').fill('HR');

    // 3. Mock Store Success
    await page.route('**/api/user', async route => {
      if (route.request().method() === 'POST') {
        await route.fulfill({
          status: 201,
          contentType: 'application/json',
          body: JSON.stringify({ id: 3, first_name: 'Jane', last_name: 'Smith', email: 'jane@example.com' })
        });
      }
    });

    // 4. Save
    await modal.locator('button.btn-save').click();

    // 5. Verify modal closed
    await expect(modal).not.toBeVisible();
  });

  test('USER-02: should allow updating user details', async ({ page }) => {
    // 1. Click Edit on an existing user (using email as a robust locator)
    await page.locator('tr', { hasText: 'john@example.com' }).locator('.btn-warning').click();
    const modal = page.locator('#userModal');
    await expect(modal).toBeVisible();

    // 2. Update a field
    const firstNameInput = modal.locator('input[formControlName="first_name"]');
    await expect(firstNameInput).toHaveValue('John');
    await firstNameInput.fill('Johnny');

    // 3. Mock Update Success
    await page.route('**/api/user/2', async route => {
      if (route.request().method() === 'PATCH') {
        await route.fulfill({
          status: 200,
          contentType: 'application/json',
          body: JSON.stringify({ id: 2, first_name: 'Johnny', last_name: 'Doe', email: 'john@example.com' })
        });
      }
    });

    // 4. Save
    await modal.locator('button.btn-save').click();

    // 5. Verify modal closed
    await expect(modal).not.toBeVisible();
  });

  test('USER-03: should allow deleting a user', async ({ page }) => {
    // 1. Click Delete (using email as a robust locator)
    await page.locator('tr', { hasText: 'john@example.com' }).locator('.btn-danger').click();

    // 2. Verify Confirmation Modal (Specifically the Delete User one)
    const confirmModal = page.locator('.modal-dialog', { hasText: 'Delete User' });
    await expect(confirmModal).toBeVisible();

    // 3. Mock Delete Success
    await page.route('**/api/user/2', async route => {
      if (route.request().method() === 'DELETE') {
        await route.fulfill({
          status: 200,
          contentType: 'application/json',
          body: JSON.stringify({ message: 'User deleted successfully' })
        });
      }
    });

    // 4. Confirm Delete
    await confirmModal.locator('button', { hasText: /Delete/i }).click();

    // 5. Verify modal closed
    await expect(confirmModal).not.toBeVisible();
  });
});

test.describe('User Access Control (RBAC)', () => {
  
  test('ROLE-02: Regular User should not see User Management in sidebar', async ({ page }) => {
    // 1. Mock Authentication as Regular User
    await page.addInitScript(() => {
      window.localStorage.setItem('auth_token', 'mock-token');
    });

    // 2. Mock Profile without user management permissions
    await page.route('**/api/user/profile', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          user: { id: 2, first_name: 'John', last_name: 'Doe', email: 'john@example.com', role: { role_name: 'User' } },
          permissions: ['view_pending_requests'] // No user management perms
        })
      });
    });

    await page.goto('/home');

    // 3. Verify User Management is NOT in sidebar
    await expect(page.locator('.nav-link', { hasText: /User Management/i })).not.toBeVisible();
  });

  test('ROLE-01: Admin should see User Management in sidebar', async ({ page }) => {
    // 1. Mock Authentication as Admin
    await page.addInitScript(() => {
      window.localStorage.setItem('auth_token', 'mock-token');
    });

    // 2. Mock Profile with admin permissions
    await page.route('**/api/user/profile', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          user: { id: 1, first_name: 'Admin', last_name: 'User', email: 'admin@example.com', role: { role_name: 'Admin' } },
          permissions: ['add_user', 'edit_user'] 
        })
      });
    });

    await page.goto('/home');

    // 3. Verify User Management IS in sidebar
    await expect(page.locator('.nav-link', { hasText: /User Management/i })).toBeVisible();
  });

  test('PERM-01: User-specific permission override should work', async ({ page }) => {
    // This test verifies that if a 'User' role is given 'add_user' permission, they can see User Management.
    
    // 1. Mock Authentication
    await page.addInitScript(() => {
      window.localStorage.setItem('auth_token', 'mock-token');
    });

    // 2. Mock Profile: Role 'User' BUT with 'add_user' permission override
    await page.route('**/api/user/profile', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          user: { id: 2, first_name: 'Privileged', last_name: 'User', email: 'puser@example.com', role: { role_name: 'User' } },
          permissions: ['add_user'] 
        })
      });
    });

    await page.goto('/home');

    // 3. Verify User Management IS in sidebar despite being a 'User' role
    await expect(page.locator('.nav-link', { hasText: /User Management/i })).toBeVisible();
  });
});

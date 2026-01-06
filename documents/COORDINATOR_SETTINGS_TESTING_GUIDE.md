# Coordinator Settings - Testing Guide

## How to Test the New Features

### Prerequisites
- Be logged in as a Coordinator user
- Navigate to: `/coordinator/settings`

---

## Test 1: Profile Update (UPDATE Operation)

### Steps:
1. Navigate to **Settings** page
2. Click on **Profile Settings** tab (should be active by default)
3. Update the following fields:
   - First Name
   - Last Name
   - Email
   - Phone Number
4. Click **Save Changes**

### Expected Results:
✅ Success message: "Profile updated successfully!"
✅ Page reloads with updated information
✅ Both `tbl_user` and `tbl_coordinator` tables are updated

### SQL Queries Executed:
```sql
UPDATE tbl_user 
SET first_name = ?, last_name = ?, email = ?, contact_number = ? 
WHERE user_id = ?

UPDATE tbl_coordinator 
SET first_name = ?, last_name = ?, email = ? 
WHERE coordinator_id = ?
```

---

## Test 2: Password Update (UPDATE Operation)

### Steps:
1. Navigate to **Settings** page
2. Click on **Security** tab
3. Fill in the form:
   - Current Password
   - New Password (min 8 characters)
   - Confirm New Password
4. Click **Update Password**

### Expected Results:
✅ Success message: "Password updated successfully!"
✅ Password is changed in database (hashed)
✅ Can login with new password

### SQL Query Executed:
```sql
UPDATE tbl_user 
SET password = ? 
WHERE user_id = ?
```

---

## Test 3: Validation Testing

### Email Validation:
1. Try to update email to one that already exists
   - Expected: Error message about email already taken

### Password Validation:
1. Enter wrong current password
   - Expected: "Current password is incorrect."

2. Enter password less than 8 characters
   - Expected: Validation error about minimum length

3. Enter mismatched password confirmation
   - Expected: Validation error about password confirmation

### Required Fields:
1. Leave First Name empty and submit
   - Expected: Validation error

2. Leave Email empty and submit
   - Expected: Validation error

---

## Test 4: Read-Only Fields

### Verify These Cannot Be Changed:
- ✅ **Position**: Should show "Coordinator" (read-only)
- ✅ **School**: Should show coordinator's school name (read-only)

---

## Test 5: Features That Were Removed

### Verify These Are Gone:
- ❌ **Change Photo button** - Should not be present
- ❌ **Department field** - Should not be present
- ❌ **Location field** - Should not be present

---

## Database Verification

### Check `tbl_user` table:
```sql
SELECT user_id, first_name, last_name, email, contact_number, password 
FROM tbl_user 
WHERE user_id = [your_coordinator_user_id];
```

### Check `tbl_coordinator` table:
```sql
SELECT coordinator_id, first_name, last_name, email 
FROM tbl_coordinator 
WHERE coordinator_id = [your_coordinator_id];
```

### Verify Both Tables Updated:
After profile update, both tables should have the same:
- first_name
- last_name
- email

---

## Routes to Test

| Method | Route | Controller Method | Purpose |
|--------|-------|------------------|---------|
| GET | `/coordinator/settings` | `CoordinatorController@settings` | Display settings page |
| POST | `/coordinator/update-profile` | `CoordinatorController@updateProfile` | Update profile |
| POST | `/coordinator/update-password` | `CoordinatorController@updatePassword` | Update password |

---

## Edge Cases to Test

1. **Coordinator without school:**
   - Should show "N/A" in school field

2. **User without coordinator record:**
   - Profile update should only update `tbl_user`
   - Should not crash

3. **Very long names:**
   - Test with 50+ characters (should be truncated/validated)

4. **Special characters in email:**
   - Test email validation

5. **SQL Injection attempt:**
   - Try entering SQL in form fields
   - Expected: Laravel's Eloquent should prevent it

---

## Browser Console Testing

### Check for JavaScript Errors:
1. Open browser Developer Tools (F12)
2. Go to Console tab
3. Perform all tests above
4. Expected: No JavaScript errors

---

## Success Criteria

✅ All profile updates save correctly to database
✅ Password updates work and are hashed
✅ Validation errors display properly
✅ Success messages appear after successful updates
✅ No removed features are present
✅ Read-only fields cannot be edited
✅ Both tbl_user and tbl_coordinator stay in sync

---

## Troubleshooting

### If profile doesn't update:
1. Check if CSRF token is present in form
2. Verify route is registered: `php artisan route:list --name=coordinator`
3. Check Laravel logs: `storage/logs/laravel.log`

### If password doesn't change:
1. Verify current password is correct
2. Check password is at least 8 characters
3. Ensure password confirmation matches

### If validation doesn't work:
1. Check browser console for errors
2. Verify form has `@csrf` token
3. Check if JavaScript is enabled

---

**Testing Date:** January 6, 2026  
**Tester:** [Your Name]  
**Status:** [ ] Pass / [ ] Fail

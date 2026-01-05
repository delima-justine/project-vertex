# User Management Refactor - Coordinator Integration

## Overview
Refactored the HR User Management system to automatically create and manage `tbl_coordinator` records when creating/updating Coordinator users.

---

## Changes Made

### 1. **UserController.php** (`app/Http/Controllers/HR/UserController.php`)

#### Added Imports:
```php
use App\Models\Coordinator;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
```

#### Updated Methods:

**`create()` Method:**
- Now fetches all schools from database
- Passes `$schools` to the view for selection dropdown

**`store()` Method (Create User):**
- Added `school_id` validation
- Uses **Database Transaction** for data integrity
- **When role is 'coordinator':**
  1. Generates a unique 8-character key (e.g., "A5B3C7D9")
  2. Creates entry in `tbl_coordinator` table with:
     - first_name, last_name, email
     - school_id
     - unique_key (for registration/identification)
  3. Links the User record to Coordinator via `coordinator_id`
- **For other roles:** Creates User normally
- Rollback on error to prevent partial data

**`edit()` Method:**
- Fetches all schools for the edit form

**`update()` Method (Update User):**
- Added `school_id` validation
- Uses **Database Transaction**
- Handles **3 scenarios:**

  1. **User becomes Coordinator** (wasn't coordinator before):
     - Creates new `tbl_coordinator` record
     - Generates unique_key
     - Links user to coordinator

  2. **User is already Coordinator** (updating coordinator):
     - Updates existing `tbl_coordinator` record
     - Syncs first_name, last_name, email, school_id
     - Keeps the same unique_key

  3. **User changes from Coordinator to another role**:
     - Removes `coordinator_id` link from user
     - Keeps coordinator record in database (for historical data)

---

### 2. **create.blade.php** (`resources/views/hr/users/create.blade.php`)

#### Added School Selection Field:
```blade
<div class="col-12" id="schoolField">
    <label class="form-label fw-bold">School 
        <small class="text-muted">(Required for Coordinators and Students)</small>
    </label>
    <select name="school_id" class="form-select">
        <option value="">-- Select School --</option>
        @foreach($schools as $school)
            <option value="{{ $school->school_id }}">
                {{ $school->school_name }} 
                @if($school->branch_campus) - {{ $school->branch_campus }} @endif
            </option>
        @endforeach
    </select>
</div>
```

#### Added JavaScript Toggle:
- Shows/hides school field based on role selection
- Only displays for "Coordinator" and "Student" roles
- Automatically hides for "Admin" and "HR" roles

---

### 3. **edit.blade.php** (`resources/views/hr/users/edit.blade.php`)

#### Added School Selection Field:
- Same as create form
- Pre-selects current school if assigned

#### Added JavaScript Toggle:
- Same functionality as create form

---

## How It Works - Flow Diagram

### Creating a Coordinator User

```
┌─────────────────────────────────────────────────────────────┐
│  HR fills form:                                             │
│  - First Name: John                                         │
│  - Last Name: Doe                                           │
│  - Email: john.doe@school.edu                               │
│  - Role: Coordinator                                        │
│  - School: ABC University                                   │
│  - Password: ******                                         │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│           UserController::store() - BEGIN TRANSACTION       │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│  Step 1: Check if role === 'coordinator'                   │
│  ✓ YES                                                      │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│  Step 2: Generate unique_key                                │
│  $uniqueKey = "A5B3C7D9" (random 8 chars)                  │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│  Step 3: Insert into tbl_coordinator                        │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ coordinator_id: 1 (auto-increment)                   │  │
│  │ first_name: "John"                                   │  │
│  │ last_name: "Doe"                                     │  │
│  │ email: "john.doe@school.edu"                         │  │
│  │ school_id: 5                                         │  │
│  │ unique_key: "A5B3C7D9"                               │  │
│  │ created_at: 2026-01-05 09:00:00                      │  │
│  └──────────────────────────────────────────────────────┘  │
│  Returns: coordinator_id = 1                                │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│  Step 4: Insert into tbl_user                               │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ user_id: 10 (auto-increment)                         │  │
│  │ first_name: "John"                                   │  │
│  │ last_name: "Doe"                                     │  │
│  │ email: "john.doe@school.edu"                         │  │
│  │ password: [hashed]                                   │  │
│  │ user_role: "Coordinator"                             │  │
│  │ school_id: 5                                         │  │
│  │ coordinator_id: 1  ← LINKED TO COORDINATOR           │  │
│  │ created_at: 2026-01-05 09:00:00                      │  │
│  └──────────────────────────────────────────────────────┘  │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│           COMMIT TRANSACTION                                 │
│           ✓ Both records created successfully               │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│  Redirect to users list with success message                │
└─────────────────────────────────────────────────────────────┘
```

---

## Database Relationship

### Before Creating User:
```
tbl_user: EMPTY
tbl_coordinator: EMPTY
```

### After Creating Coordinator User:

**tbl_coordinator:**
| coordinator_id | first_name | last_name | email              | school_id | unique_key |
|----------------|------------|-----------|--------------------| ----------|------------|
| 1              | John       | Doe       | john@school.edu    | 5         | A5B3C7D9   |

**tbl_user:**
| user_id | first_name | last_name | email           | user_role   | school_id | coordinator_id |
|---------|------------|-----------|------------------|-------------|-----------|----------------|
| 10      | John       | Doe       | john@school.edu  | Coordinator | 5         | 1              |

**Relationship:**
```
tbl_user.coordinator_id → tbl_coordinator.coordinator_id
```

---

## Update Scenarios

### Scenario 1: Updating Coordinator Info
**User changes coordinator's name or school:**
- Updates both `tbl_user` and `tbl_coordinator` tables
- Keeps the same `unique_key`

### Scenario 2: Changing User from Coordinator to HR
**User role changed from "Coordinator" to "HR":**
- Sets `tbl_user.coordinator_id = NULL`
- Keeps `tbl_coordinator` record intact (for historical data)
- User can no longer access coordinator features

### Scenario 3: Changing User from HR to Coordinator
**User role changed from "HR" to "Coordinator":**
- Creates new `tbl_coordinator` record
- Generates new `unique_key`
- Links user to new coordinator record

---

## Key Features

### ✅ Transaction Safety
- Uses `DB::beginTransaction()` and `DB::commit()`
- Automatically rolls back if any error occurs
- Prevents partial data (user created but coordinator not created)

### ✅ Unique Key Generation
- Each coordinator gets a unique 8-character code
- Used for registration links or identification
- Format: Uppercase alphanumeric (e.g., "A5B3C7D9")

### ✅ Data Synchronization
- When updating coordinator user, both tables stay in sync
- Email, name changes reflect in both tables

### ✅ School Assignment
- Coordinators and Students can be assigned to schools
- Admin and HR roles don't require school assignment

### ✅ Dynamic UI
- School field only shows when needed (Coordinator/Student)
- JavaScript automatically toggles visibility

---

## Example Usage

### Create a Coordinator:
1. Go to HR Dashboard → Users → Create User
2. Fill in:
   - First Name: Jane
   - Last Name: Smith
   - Email: jane@university.edu
   - Role: **Coordinator**
   - School: Select from dropdown
   - Password: Set password
3. Click "Create User"
4. System automatically:
   - Creates record in `tbl_coordinator` with unique_key
   - Creates record in `tbl_user` linked to coordinator

### Update Coordinator:
1. Edit existing coordinator user
2. Change name/email/school
3. System updates both tables

### Change Role from Coordinator:
1. Edit coordinator user
2. Change role to "HR"
3. System unlinks coordinator but keeps record

---

## Error Handling

```php
try {
    DB::beginTransaction();
    // Create coordinator record
    // Create user record
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    return back()->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
}
```

If any step fails:
- ❌ All changes are rolled back
- ❌ No partial data in database
- ✅ User sees error message
- ✅ Form data is preserved for retry

---

## Benefits

1. **Data Integrity**: Transaction ensures both tables are always in sync
2. **Automatic**: No manual coordinator record creation needed
3. **Flexible**: Handles role changes gracefully
4. **Safe**: Rollback prevents database corruption
5. **User-Friendly**: Clear school selection with automatic visibility toggle

---

## Testing Checklist

- [ ] Create new Coordinator user
- [ ] Verify record in both `tbl_user` and `tbl_coordinator`
- [ ] Check `unique_key` is generated
- [ ] Update Coordinator's information
- [ ] Verify both tables updated
- [ ] Change Coordinator to another role
- [ ] Verify `coordinator_id` removed from user
- [ ] Change non-Coordinator to Coordinator
- [ ] Verify new coordinator record created
- [ ] Test with missing school (should still work as optional)
- [ ] Test form validation errors
- [ ] Test transaction rollback on error

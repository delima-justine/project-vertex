# Multi-Role Registration System - Database Queries
## Laravel Eloquent & SQL Equivalents

**Document Date:** January 6, 2026  
**Project:** InternConnect  
**Database:** MySQL with Laravel Eloquent ORM  
**Status:** Active Implementation

---

## Overview

The multi-role registration system uses **Laravel Eloquent ORM** for all database operations, which provides:
- ✅ **Type Safety** – Automatic type casting
- ✅ **SQL Injection Prevention** – Parameterized queries
- ✅ **Relationship Management** – Built-in foreign key handling
- ✅ **Transaction Support** – Atomic operations for consistency

---

## 1. Database Schema

### Table Structure

#### `tbl_school`
```sql
CREATE TABLE tbl_school (
    school_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_name VARCHAR(100) NULL,
    branch_campus VARCHAR(100) NULL,
    address VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### `tbl_coordinator`
```sql
CREATE TABLE tbl_coordinator (
    coordinator_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NULL,
    last_name VARCHAR(50) NULL,
    email VARCHAR(100) NULL,
    school_id BIGINT UNSIGNED NULL,
    unique_key VARCHAR(50) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (school_id) REFERENCES tbl_school(school_id)
);
```

#### `tbl_user`
```sql
CREATE TABLE tbl_user (
    user_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NULL,
    last_name VARCHAR(50) NULL,
    email VARCHAR(100) UNIQUE NULL,
    contact_number VARCHAR(15) NULL,
    user_role ENUM('Intern', 'Admin', 'HR', 'Coordinator') NULL,
    school_id BIGINT UNSIGNED NULL,
    coordinator_id BIGINT UNSIGNED NULL,
    status ENUM('Applicant', 'Active', 'Completed', 'Cleared') NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (school_id) REFERENCES tbl_school(school_id),
    FOREIGN KEY (coordinator_id) REFERENCES tbl_coordinator(coordinator_id),
    UNIQUE KEY email_unique (email)
);
```

### Foreign Key Relationships
```
tbl_user.school_id → tbl_school.school_id
tbl_user.coordinator_id → tbl_coordinator.coordinator_id
tbl_coordinator.school_id → tbl_school.school_id
```

---

## 2. Registration Queries by User Role

### Scenario A: Register as INTERN with Existing School

#### Laravel Eloquent (Recommended)
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::create([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john@university.edu',
    'contact_number' => '09171234567',
    'user_role' => 'Intern',
    'school_id' => 5,  // Existing school ID
    'status' => 'Applicant',
    'password' => Hash::make('securepassword123'),
]);

// Returns: User object with user_id auto-generated
// $user->user_id = (newly created ID)
```

#### Equivalent Raw SQL
```sql
INSERT INTO tbl_user (
    first_name, 
    last_name, 
    email, 
    contact_number, 
    user_role, 
    school_id, 
    status, 
    password, 
    created_at, 
    updated_at
) VALUES (
    'John',
    'Doe',
    'john@university.edu',
    '09171234567',
    'Intern',
    5,
    'Applicant',
    '$2y$12$...',  -- Bcrypt hashed password
    NOW(),
    NOW()
);
```

---

### Scenario B: Register as INTERN with New School

#### Laravel Eloquent
```php
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

DB::transaction(function () {
    // Step 1: Create new school
    $school = School::create([
        'school_name' => 'De La Salle University',
        'branch_campus' => 'Dasmarinas Campus',
        'address' => '2401 Avocado Street, Dasmarinas, Cavite'
    ]);

    // Step 2: Create user with new school reference
    $user = User::create([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'jane@dlsu.edu',
        'contact_number' => '09201234567',
        'user_role' => 'Intern',
        'school_id' => $school->school_id,  // Use newly created school
        'status' => 'Applicant',
        'password' => Hash::make('securepassword456'),
    ]);

    return $user;
});
```

#### Equivalent Raw SQL
```sql
-- Transaction starts
START TRANSACTION;

-- Step 1: Insert new school
INSERT INTO tbl_school (school_name, branch_campus, address, created_at, updated_at)
VALUES (
    'De La Salle University',
    'Dasmarinas Campus',
    '2401 Avocado Street, Dasmarinas, Cavite',
    NOW(),
    NOW()
);

-- Get last inserted school_id
SET @school_id = LAST_INSERT_ID();

-- Step 2: Insert user with school reference
INSERT INTO tbl_user (
    first_name, 
    last_name, 
    email, 
    contact_number, 
    user_role, 
    school_id, 
    status, 
    password, 
    created_at, 
    updated_at
) VALUES (
    'Jane',
    'Smith',
    'jane@dlsu.edu',
    '09201234567',
    'Intern',
    @school_id,
    'Applicant',
    '$2y$12$...',  -- Bcrypt hash
    NOW(),
    NOW()
);

-- Transaction commits
COMMIT;
```

---

### Scenario C: Register as COORDINATOR with Existing School

#### Laravel Eloquent
```php
use App\Models\User;
use App\Models\Coordinator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

DB::transaction(function () {
    // Step 1: Create coordinator record
    $randomDigits = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
    $coordinatorId = 'COORD-2026-' . $randomDigits;

    $coordinator = Coordinator::create([
        'first_name' => 'Maria',
        'last_name' => 'Garcia',
        'email' => 'maria@university.edu',
        'school_id' => 5,  // Existing school
        'unique_key' => $coordinatorId,
    ]);

    // Step 2: Create user record linked to coordinator
    $user = User::create([
        'first_name' => 'Maria',
        'last_name' => 'Garcia',
        'email' => 'maria@university.edu',
        'contact_number' => '09231234567',
        'user_role' => 'Coordinator',
        'school_id' => 5,
        'coordinator_id' => $coordinator->coordinator_id,
        'status' => 'Active',
        'password' => Hash::make('securepassword789'),
    ]);

    return $user;
});
```

#### Equivalent Raw SQL
```sql
START TRANSACTION;

-- Step 1: Insert coordinator
INSERT INTO tbl_coordinator (
    first_name, 
    last_name, 
    email, 
    school_id, 
    unique_key, 
    created_at, 
    updated_at
) VALUES (
    'Maria',
    'Garcia',
    'maria@university.edu',
    5,
    'COORD-2026-7432',
    NOW(),
    NOW()
);

SET @coordinator_id = LAST_INSERT_ID();

-- Step 2: Insert user linked to coordinator
INSERT INTO tbl_user (
    first_name, 
    last_name, 
    email, 
    contact_number, 
    user_role, 
    school_id, 
    coordinator_id, 
    status, 
    password, 
    created_at, 
    updated_at
) VALUES (
    'Maria',
    'Garcia',
    'maria@university.edu',
    '09231234567',
    'Coordinator',
    5,
    @coordinator_id,
    'Active',
    '$2y$12$...',
    NOW(),
    NOW()
);

COMMIT;
```

---

### Scenario D: Register as COORDINATOR with New School

#### Laravel Eloquent
```php
use App\Models\User;
use App\Models\School;
use App\Models\Coordinator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

DB::transaction(function () {
    // Step 1: Create new school
    $school = School::create([
        'school_name' => 'Ateneo de Manila University',
        'branch_campus' => 'Main Campus',
        'address' => 'Loyola Heights, Quezon City'
    ]);

    // Step 2: Create coordinator
    $randomDigits = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
    $coordinatorId = 'COORD-2026-' . $randomDigits;

    $coordinator = Coordinator::create([
        'first_name' => 'Rafael',
        'last_name' => 'Santos',
        'email' => 'rafael@ateneo.edu',
        'school_id' => $school->school_id,
        'unique_key' => $coordinatorId,
    ]);

    // Step 3: Create user
    $user = User::create([
        'first_name' => 'Rafael',
        'last_name' => 'Santos',
        'email' => 'rafael@ateneo.edu',
        'contact_number' => '09351234567',
        'user_role' => 'Coordinator',
        'school_id' => $school->school_id,
        'coordinator_id' => $coordinator->coordinator_id,
        'status' => 'Active',
        'password' => Hash::make('securepassword101'),
    ]);

    return $user;
});
```

#### Equivalent Raw SQL
```sql
START TRANSACTION;

-- Step 1: Insert new school
INSERT INTO tbl_school (school_name, branch_campus, address, created_at, updated_at)
VALUES (
    'Ateneo de Manila University',
    'Main Campus',
    'Loyola Heights, Quezon City',
    NOW(),
    NOW()
);

SET @school_id = LAST_INSERT_ID();

-- Step 2: Insert coordinator with new school
INSERT INTO tbl_coordinator (
    first_name, 
    last_name, 
    email, 
    school_id, 
    unique_key, 
    created_at, 
    updated_at
) VALUES (
    'Rafael',
    'Santos',
    'rafael@ateneo.edu',
    @school_id,
    'COORD-2026-5821',
    NOW(),
    NOW()
);

SET @coordinator_id = LAST_INSERT_ID();

-- Step 3: Insert user linked to new school and coordinator
INSERT INTO tbl_user (
    first_name, 
    last_name, 
    email, 
    contact_number, 
    user_role, 
    school_id, 
    coordinator_id, 
    status, 
    password, 
    created_at, 
    updated_at
) VALUES (
    'Rafael',
    'Santos',
    'rafael@ateneo.edu',
    '09351234567',
    'Coordinator',
    @school_id,
    @coordinator_id,
    'Active',
    '$2y$12$...',
    NOW(),
    NOW()
);

COMMIT;
```

---

## 3. Actual Implementation: RegisterUserAction

The system uses a Laravel **Action Class** pattern for clean, reusable registration logic:

### Full Implementation
```php
<?php

namespace App\Actions;

use App\Models\User;
use App\Models\Coordinator;
use App\Models\School;
use Illuminate\Support\Facades\DB;

class RegisterUserAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Handle new school creation
            if ($data['selected_school'] === 'other') {
                $school = School::create([
                    'school_name' => $data['new_school_name'],
                    'address' => $data['new_school_address'],
                    'branch_campus' => $data['new_school_campus'] ?? null,
                ]);
                $data['selected_school'] = $school->school_id;
            }

            if ($data['user_role'] === 'Coordinator') {
                // Generate unique coordinator ID: COORD-2026-XXXX
                $randomDigits = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $coordinatorId = 'COORD-2026-' . $randomDigits;

                // Create Coordinator record
                $coordinator = Coordinator::create([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'school_id' => $data['selected_school'],
                    'unique_key' => $coordinatorId,
                ]);

                // Create User record
                $user = User::create([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'contact_number' => $data['contact_number'],
                    'user_role' => 'Coordinator',
                    'school_id' => $data['selected_school'],
                    'coordinator_id' => $coordinator->coordinator_id,
                    'status' => 'Active',
                    'password' => $data['password'],
                ]);

                return $user;
            } elseif ($data['user_role'] === 'Intern') {
                // Create User record for Intern
                $user = User::create([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'contact_number' => $data['contact_number'],
                    'user_role' => 'Intern',
                    'school_id' => $data['selected_school'],
                    'status' => 'Applicant',
                    'password' => $data['password'],
                ]);

                return $user;
            }

            throw new \Exception('Invalid role specified.');
        });
    }
}
```

---

## 4. Query Operations (CRUD)

### CREATE: Register New User

**Livewire Component Usage:**
```php
// app/Livewire/Register.php
public function register()
{
    $this->validate();

    RegisterUserAction::execute([
        'user_role' => $this->user_role,
        'first_name' => $this->first_name,
        'last_name' => $this->last_name,
        'email' => $this->email,
        'contact_number' => $this->contact_number,
        'password' => Hash::make($this->password),
        'selected_school' => $this->selected_school,
        'new_school_name' => $this->new_school_name,
        'new_school_address' => $this->new_school_address,
        'new_school_campus' => $this->new_school_campus,
    ]);

    return redirect($this->getRedirectPath());
}
```

---

### READ: Fetch Existing Data

#### Get All Schools (for Dropdown)
```php
// Eloquent
$schools = School::all();

// SQL Equivalent
SELECT * FROM tbl_school;
```

#### Get User by Email
```php
// Eloquent
$user = User::where('email', 'john@university.edu')->first();

// SQL Equivalent
SELECT * FROM tbl_user WHERE email = 'john@university.edu' LIMIT 1;
```

#### Get Coordinator by ID with School
```php
// Eloquent (with relationship eager loading)
$coordinator = Coordinator::with('school')->find($coordinator_id);

// SQL Equivalent
SELECT c.*, s.* FROM tbl_coordinator c
LEFT JOIN tbl_school s ON c.school_id = s.school_id
WHERE c.coordinator_id = ?;
```

#### Get All Users by Role
```php
// Eloquent
$interns = User::where('user_role', 'Intern')->get();
$coordinators = User::where('user_role', 'Coordinator')->get();

// SQL Equivalent
SELECT * FROM tbl_user WHERE user_role = 'Intern';
SELECT * FROM tbl_user WHERE user_role = 'Coordinator';
```

#### Get All Users from a School
```php
// Eloquent
$schoolUsers = User::where('school_id', 5)->get();

// SQL Equivalent
SELECT * FROM tbl_user WHERE school_id = 5;
```

---

### UPDATE: Modify User Status

#### Change Intern Status
```php
// Eloquent
$user = User::find($user_id);
$user->update(['status' => 'Active']);  // Change from Applicant to Active

// Alternative (single query)
User::where('user_id', $user_id)
    ->update(['status' => 'Active']);

// SQL Equivalent
UPDATE tbl_user SET status = 'Active' WHERE user_id = ?;
```

#### Update Multiple Users
```php
// Eloquent
User::where('school_id', 5)
    ->where('user_role', 'Intern')
    ->update(['status' => 'Cleared']);

// SQL Equivalent
UPDATE tbl_user 
SET status = 'Cleared' 
WHERE school_id = 5 AND user_role = 'Intern';
```

---

### DELETE: Remove User

#### Delete Single User
```php
// Eloquent
$user = User::find($user_id);
$user->delete();

// Alternative (single query)
User::where('user_id', $user_id)->delete();

// SQL Equivalent
DELETE FROM tbl_user WHERE user_id = ?;
```

**⚠️ Cascading Impact:**
- If a User is deleted and has `coordinator_id` reference, the orphaned `tbl_coordinator` record remains
- Consider soft deletes or cascade policies for data integrity

---

## 5. Validation Queries

### Check Email Uniqueness (Registration)
```php
// Eloquent (in Livewire validation rules)
'email' => [
    'required',
    'email',
    Rule::unique('tbl_user', 'email')  // Native Eloquent validation
]

// SQL Equivalent (manual check)
SELECT COUNT(*) FROM tbl_user WHERE email = ? LIMIT 1;
// If count > 0, email already exists
```

### Check School Existence
```php
// Eloquent
$schoolExists = School::find($school_id) !== null;

// SQL Equivalent
SELECT COUNT(*) FROM tbl_school WHERE school_id = ? LIMIT 1;
```

---

## 6. Relationship Queries

### Get User with School and Coordinator Info
```php
// Eloquent (eager loading to prevent N+1 queries)
$user = User::with(['school', 'coordinator'])->find($user_id);

// Access relationships
echo $user->school->school_name;
echo $user->coordinator->first_name;

// SQL Equivalent (single query with joins)
SELECT u.*, s.*, c.* 
FROM tbl_user u
LEFT JOIN tbl_school s ON u.school_id = s.school_id
LEFT JOIN tbl_coordinator c ON u.coordinator_id = c.coordinator_id
WHERE u.user_id = ?;
```

### Get School with All Coordinators
```php
// Eloquent
$school = School::with('coordinators')->find($school_id);
foreach ($school->coordinators as $coordinator) {
    echo $coordinator->first_name;
}

// SQL Equivalent
SELECT c.* FROM tbl_coordinator c
WHERE c.school_id = ?;
```

### Get Coordinator with School
```php
// Eloquent
$coordinator = Coordinator::with('school')->find($coordinator_id);
echo $coordinator->school->school_name;

// SQL Equivalent
SELECT c.*, s.* FROM tbl_coordinator c
LEFT JOIN tbl_school s ON c.school_id = s.school_id
WHERE c.coordinator_id = ?;
```

---

## 7. Transaction Safety Example

All registration operations are wrapped in database transactions to ensure **atomicity**:

```php
DB::transaction(function () {
    // If ANY operation fails, ALL changes rollback
    
    $school = School::create([...]);      // INSERT
    $coordinator = Coordinator::create([...]); // INSERT
    $user = User::create([...]);           // INSERT
    
    // If $user->create() fails here, school and coordinator are rolled back
});

// Raw SQL Equivalent
START TRANSACTION;
    INSERT INTO tbl_school ...;
    INSERT INTO tbl_coordinator ...;
    INSERT INTO tbl_user ...;
COMMIT;  -- All succeed together, or
ROLLBACK; -- All fail together
```

---

## 8. Performance Considerations

### Indexes
The system uses these indexes for optimal query performance:

```sql
-- Email index (for unique constraint and login lookups)
CREATE UNIQUE INDEX idx_user_email ON tbl_user(email);

-- School lookup (frequent in dropdowns)
CREATE INDEX idx_school_name ON tbl_school(school_name);

-- Foreign key lookups
CREATE INDEX idx_user_school ON tbl_user(school_id);
CREATE INDEX idx_coordinator_school ON tbl_coordinator(school_id);
```

### N+1 Query Prevention
```php
// ❌ BAD: N+1 queries
$users = User::all();
foreach ($users as $user) {
    echo $user->school->school_name;  // Extra query per user!
}

// ✅ GOOD: Eager loading
$users = User::with('school')->all();
foreach ($users as $user) {
    echo $user->school->school_name;  // No extra queries!
}
```

---

## 9. Quick Reference: Common Queries

| Operation | Eloquent | SQL |
|-----------|----------|-----|
| Create User | `User::create([...])` | `INSERT INTO tbl_user ...` |
| Get User | `User::find($id)` | `SELECT * FROM tbl_user WHERE user_id = ?` |
| Update User | `$user->update([...])` | `UPDATE tbl_user SET ... WHERE user_id = ?` |
| Delete User | `$user->delete()` | `DELETE FROM tbl_user WHERE user_id = ?` |
| Get School | `School::find($id)` | `SELECT * FROM tbl_school WHERE school_id = ?` |
| All Schools | `School::all()` | `SELECT * FROM tbl_school` |
| Users by Role | `User::where('user_role', 'Intern')->get()` | `SELECT * FROM tbl_user WHERE user_role = 'Intern'` |
| Unique Email | `User::where('email', $email)->first()` | `SELECT * FROM tbl_user WHERE email = ?` |

---

## 10. Debugging Database Queries

### Enable Query Logging
```php
// In Laravel controller or Livewire component
use Illuminate\Support\Facades\DB;

DB::enableQueryLog();

// Your queries here
$user = User::find(1);

// View executed queries
dd(DB::getQueryLog());

/* Output:
[
    [
        "query" => "select * from `tbl_user` where `user_id` = ? limit 1",
        "bindings" => [1],
        "time" => 0.45
    ]
]
*/
```

### View SQL for Eloquent Queries
```php
// Get SQL without executing
$query = User::where('email', 'test@example.com');
echo $query->toSql();
// Output: select * from `tbl_user` where `email` = ?

echo json_encode($query->getBindings());
// Output: ["test@example.com"]
```

---

## Conclusion

The InternConnect registration system leverages **Laravel Eloquent ORM** exclusively for:
- ✅ Type-safe database operations
- ✅ Automatic relationship handling
- ✅ Built-in SQL injection prevention
- ✅ Transaction management
- ✅ Convenient validation integration

All registration flows ultimately execute the provided SQL operations through Eloquent's query builder, ensuring data integrity and consistency across the multi-role system.

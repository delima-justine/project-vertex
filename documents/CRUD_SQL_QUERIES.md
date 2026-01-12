# CRUD SQL Query Documentation - InternConnect

This document contains all CRUD (Create, Read, Update, Delete) SQL queries **actually used** in the InternConnect project codebase.

---

## Table of Contents
- [User Management](#user-management)
- [Coordinator Management](#coordinator-management)
- [School Management](#school-management)
- [Job Posting Management](#job-posting-management)

---

## User Management

### Location: `app/Http/Controllers/HR/UserController.php`, `app/Http/Controllers/AuthController.php`, `app/Actions/RegisterUserAction.php`, `app/Http/Controllers/CoordinatorController.php`, `app/Http/Controllers/InternController.php`

### API Endpoints:
- `GET /hr/users` - List users (HR\UserController@index)
- `GET /hr/users/create` - Show create form (HR\UserController@create)
- `POST /hr/users` - Store new user (HR\UserController@store)
- `GET /hr/users/{user}/edit` - Show edit form (HR\UserController@edit)
- `PUT/PATCH /hr/users/{user}` - Update user (HR\UserController@update)
- `DELETE /hr/users/{user}` - Delete user (HR\UserController@destroy)
- `POST /auth/applicant/login` - Applicant login (AuthController@applicantLogin)
- `POST /coordinator/update-profile` - Update coordinator profile (CoordinatorController@updateProfile)
- `POST /coordinator/update-password` - Update coordinator password (CoordinatorController@updatePassword)
- `GET /intern/profile/{id}` - View intern profile (InternController@profile)

| Type | Query | Description |
|------|-------|-------------|
| **CREATE** | `INSERT INTO tbl_user (first_name, last_name, email, contact_number, password, user_role, school_id, coordinator_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)` | Create a new user account (used in UserController@store and RegisterUserAction) |
| **CREATE** | `INSERT INTO tbl_user (first_name, last_name, email, contact_number, password, user_role, status) VALUES (?, ?, ?, ?, ?, 'Intern', 'Applicant')` | Create applicant/intern account (used in AuthController@applicantRegister and RegisterUserAction) |
| **READ** | `SELECT * FROM tbl_user WHERE (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?) ORDER BY user_id DESC LIMIT 15 OFFSET ?` | Search and list users with pagination (UserController@index) |
| **READ** | `SELECT * FROM tbl_user WHERE user_role = 'Intern' AND coordinator_id = ?` | Get all interns assigned to a specific coordinator (CoordinatorController@dashboard, CoordinatorController@monitorInterns) |
| **READ** | `SELECT * FROM tbl_user WHERE user_id = ?` | Find user by ID (InternController@profile) |
| **UPDATE** | `UPDATE tbl_user SET first_name = ?, last_name = ?, email = ?, contact_number = ?, user_role = ?, school_id = ?, coordinator_id = ?, password = ? WHERE user_id = ?` | Update user information (UserController@update) |
| **UPDATE** | `UPDATE tbl_user SET first_name = ?, last_name = ?, email = ?, contact_number = ? WHERE user_id = ?` | Update coordinator profile (CoordinatorController@updateProfile) |
| **UPDATE** | `UPDATE tbl_user SET password = ? WHERE user_id = ?` | Update user password (UserController@update, CoordinatorController@updatePassword - conditional) |
| **DELETE** | `DELETE FROM tbl_user WHERE user_id = ?` | Delete a user account (UserController@destroy) |

---

## Coordinator Management

### Location: `app/Http/Controllers/HR/UserController.php`, `app/Actions/RegisterUserAction.php`, `app/Http/Controllers/CoordinatorController.php`

### API Endpoints:
- `POST /hr/users` - Create coordinator when role is coordinator (HR\UserController@store)
- `PUT/PATCH /hr/users/{user}` - Update coordinator (HR\UserController@update)
- `GET /coordinator/settings` - View coordinator settings (CoordinatorController@settings)
- `POST /coordinator/update-profile` - Update coordinator profile (CoordinatorController@updateProfile)

| Type | Query | Description |
|------|-------|-------------|
| **CREATE** | `INSERT INTO tbl_coordinator (first_name, last_name, email, school_id, unique_key) VALUES (?, ?, ?, ?, ?)` | Create a new coordinator record (used in UserController@store, UserController@update, and RegisterUserAction@execute) |
| **READ** | `SELECT c.*, s.school_name, s.branch_campus, s.address FROM tbl_coordinator c LEFT JOIN tbl_school s ON c.school_id = s.school_id` | Get all coordinators with school information (UserController@create, UserController@edit) |
| **READ** | `SELECT * FROM tbl_coordinator WHERE coordinator_id = ?` | Find coordinator by ID (UserController@update, CoordinatorController@settings) |
| **UPDATE** | `UPDATE tbl_coordinator SET first_name = ?, last_name = ?, email = ?, school_id = ? WHERE coordinator_id = ?` | Update coordinator information (UserController@update) |
| **UPDATE** | `UPDATE tbl_coordinator SET first_name = ?, last_name = ?, email = ? WHERE coordinator_id = ?` | Update coordinator profile (CoordinatorController@updateProfile) |

---

## School Management

### Location: `app/Http/Controllers/HR/UserController.php`, `app/Actions/RegisterUserAction.php`, `app/Livewire/Register.php`

### API Endpoints:
- `GET /hr/users/create` - List schools for user creation (HR\UserController@create)
- `GET /hr/users/{user}/edit` - List schools for user editing (HR\UserController@edit)
- `GET /auth/register` - List schools for registration (Register@mount - Livewire)

| Type | Query | Description |
|------|-------|-------------|
| **CREATE** | `INSERT INTO tbl_school (school_name, address, branch_campus) VALUES (?, ?, ?)` | Create a new school/partner institution (RegisterUserAction@execute) |
| **READ** | `SELECT * FROM tbl_school` | Get all schools (UserController@create, UserController@edit, Register@mount) |

---

## Job Posting Management

### Location: `app/Http/Controllers/HR/JobPostingController.php`, `app/Http/Controllers/InternController.php`

### API Endpoints:
- `GET /hr/job-postings` - List all job postings (HR\JobPostingController@index)
- `GET /hr/job-postings/create` - Show create form (HR\JobPostingController@create)
- `POST /hr/job-postings` - Store new job posting (HR\JobPostingController@store)
- `GET /intern/job-search` - List jobs for interns (InternController@getJobs)

| Type | Query | Description |
|------|-------|-------------|
| **CREATE** | `INSERT INTO tbl_job_posting (title, description, requirements, department, salary_range, posted_by_user_id, post_date) VALUES (?, ?, ?, ?, ?, ?, ?)` | Create a new job posting (JobPostingController@store) |
| **READ** | `SELECT jp.*, COUNT(ja.application_id) as applications_count FROM tbl_job_posting jp LEFT JOIN tbl_job_application ja ON jp.job_id = ja.job_id GROUP BY jp.job_id ORDER BY jp.post_date DESC` | Get all job postings with application counts (JobPostingController@index) |
| **READ** | `SELECT * FROM tbl_job_posting ORDER BY created_at DESC LIMIT 10 OFFSET ?` | Get paginated job postings for intern job search (InternController@getJobs) |

---

## Additional Queries via Eloquent Relationships

### Location: `app/Http/Controllers/CoordinatorController.php`

### API Endpoints:
- `GET /coordinator/dashboard` - Coordinator dashboard with statistics (CoordinatorController@dashboard)
- `GET /coordinator/monitor-interns` - Monitor interns page (CoordinatorController@monitorInterns)

| Type | Query | Description |
|------|-------|-------------|
| **READ** | `SELECT * FROM tbl_user WHERE user_role = 'Intern' AND coordinator_id = ?` | Get interns for coordinator (eager loaded with relationships in dashboard and monitorInterns) |
| **READ** | `SELECT * FROM tbl_progress WHERE user_id IN (...)` | Get progress records for interns (eager loaded via `with(['progress'])`) |
| **READ** | `SELECT * FROM tbl_attendance WHERE user_id IN (...)` | Get attendance records for interns (eager loaded via `with(['attendances'])`) |
| **READ** | `SELECT * FROM tbl_attendance WHERE user_id = ? ORDER BY created_at DESC LIMIT 3` | Get recent attendance for dashboard activity (CoordinatorController@dashboard) |
| **READ** | `SELECT * FROM tbl_document WHERE user_id IN (...)` | Get document records for interns (eager loaded via `with(['documents'])`) |
| **READ** | `SELECT * FROM tbl_document WHERE user_id IN (...) AND verification_status = 'Pending'` | Count pending documents for dashboard (CoordinatorController@dashboard) |
| **READ** | `SELECT * FROM tbl_document WHERE user_id = ? ORDER BY submission_date DESC LIMIT 2` | Get recent document submissions for dashboard activity (CoordinatorController@dashboard) |
| **READ** | `SELECT ja.*, jp.* FROM tbl_job_application ja LEFT JOIN tbl_job_posting jp ON ja.job_id = jp.job_id WHERE ja.user_id IN (...)` | Get job applications with job details (eager loaded via `with(['jobApplications.job'])`) |

---

## Notes

1. **Laravel Eloquent ORM**: This project uses Laravel's Eloquent ORM. The SQL queries shown are the actual database queries generated by Eloquent methods like `create()`, `where()`, `update()`, `delete()`, `paginate()`, and `with()`.

2. **Query Location**: Each section includes the file path where these queries are executed in the codebase.

3. **Prepared Statements**: All queries use parameterized placeholders (`?`) to prevent SQL injection attacks.

4. **Eager Loading**: The `with()` method is used for eager loading relationships to prevent N+1 query problems.

5. **Transaction Support**: Critical operations use `DB::transaction()` for data consistency (e.g., UserController@store, RegisterUserAction@execute).

6. **Missing CRUD Operations**: Some tables (tbl_attendance, tbl_document, tbl_notification, tbl_progress, tbl_job_application) have model definitions but no controller implementations yet. They are prepared for future development.

---

**Generated for:** InternConnect - ROC.ph Internship Management System  
**Last Updated:** January 12, 2026  
**Database:** db_internconnect (MariaDB)  
**Based on:** Actual code analysis from project files

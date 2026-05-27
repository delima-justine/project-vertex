# SMIS Database Data Dictionary

This document provides a comprehensive overview of the database schema for the SMIS project, based on the `db_smis_20260510.sql` dump.

---

## 1. `cache`
Stores cached data for the application.

| No. | Field Name | Datatype | Constraint | Description |
|-----|------------|----------|------------|-------------|
| 1 | `key` | varchar(255) | PK | Unique key for the cache entry. |
| 2 | `value` | mediumtext | | The cached data. |
| 3 | `expiration` | int | | Expiration timestamp of the cache entry. |

---

## 2. `cache_locks`
Stores atomic locks for cache operations.

| No. | Field Name | Datatype | Constraint | Description |
|-----|------------|----------|------------|-------------|
| 1 | `key` | varchar(255) | PK | Unique key for the cache lock. |
| 2 | `owner` | varchar(255) | | Owner of the lock. |
| 3 | `expiration` | int | | Expiration timestamp of the lock. |

---

## 3. `failed_jobs`
Stores information about queue jobs that failed to execute.

| No. | Field Name | Datatype | Constraint | Description |
|-----|------------|----------|------------|-------------|
| 1 | `id` | bigint unsigned | PK | Unique identifier for the failed job. |
| 2 | `uuid` | varchar(255) | Unique | Unique UUID for the job. |
| 3 | `connection` | text | | The database connection used for the job. |
| 4 | `queue` | text | | The queue the job was on. |
| 5 | `payload` | longtext | | The job payload (serialized job object). |
| 6 | `exception` | longtext | | The exception that caused the job to fail. |
| 7 | `failed_at` | timestamp | | Timestamp when the job failed. |

---

## 4. `job_batches`
Stores information about batches of queue jobs.

| No. | Field Name | Datatype | Constraint | Description |
|-----|------------|----------|------------|-------------|
| 1 | `id` | varchar(255) | PK | Unique identifier for the job batch. |
| 2 | `name` | varchar(255) | | Name of the batch. |
| 3 | `total_jobs` | int | | Total number of jobs in the batch. |
| 4 | `pending_jobs` | int | | Number of pending jobs. |
| 5 | `failed_jobs` | int | | Number of failed jobs. |
| 6 | `failed_job_ids` | longtext | | IDs of the failed jobs. |
| 7 | `options` | mediumtext | | Batch options/metadata. |
| 8 | `cancelled_at` | int | | Timestamp when the batch was cancelled. |
| 9 | `created_at` | int | | Timestamp when the batch was created. |
| 10 | `finished_at` | int | | Timestamp when the batch finished. |

---

## 5. `jobs`
Stores pending queue jobs.

| No. | Field Name | Datatype | Constraint | Description |
|-----|------------|----------|------------|-------------|
| 1 | `id` | bigint unsigned | PK | Unique identifier for the job. |
| 2 | `queue` | varchar(255) | | The queue the job is on. |
| 3 | `payload` | longtext | | The job payload (serialized job object). |
| 4 | `attempts` | tinyint unsigned | | Number of times the job has been attempted. |
| 5 | `reserved_at` | int unsigned | | Timestamp when the job was reserved. |
| 6 | `available_at` | int unsigned | | Timestamp when the job becomes available. |
| 7 | `created_at` | int unsigned | | Timestamp when the job was created. |

---

## 6. `migrations`
Tracks the database migrations that have been applied.

| No. | Field Name | Datatype | Constraint | Description |
|-----|------------|----------|------------|-------------|
| 1 | `id` | int unsigned | PK | Unique identifier for the migration. |
| 2 | `migration` | varchar(255) | | Name of the migration file. |
| 3 | `batch` | int | | Migration batch number. |

---

## 7. `password_reset_tokens`
Stores tokens used for password reset requests.

| No. | Field Name | Datatype | Constraint | Description |
|-----|------------|----------|------------|-------------|
| 1 | `email` | varchar(255) | PK | Email address associated with the token. |
| 2 | `token` | varchar(255) | | The password reset token. |
| 3 | `created_at` | timestamp | | Timestamp when the token was created. |

---

## 8. `personal_access_tokens`
Stores Laravel Sanctum personal access tokens for API authentication.

| No. | Field Name | Datatype | Constraint | Description |
|-----|------------|----------|------------|-------------|
| 1 | `id` | bigint unsigned | PK | Unique identifier for the token. |
| 2 | `tokenable_type` | varchar(255) | | Polymorphic type of the model the token belongs to. |
| 3 | `tokenable_id` | bigint unsigned | | ID of the model the token belongs to. |
| 4 | `name` | text | | Name/Description of the token. |
| 5 | `token` | varchar(64) | Unique | The hashed token value. |
| 6 | `abilities` | text | | Token abilities/permissions. |
| 7 | `last_used_at` | timestamp | | Timestamp when the token was last used. |
| 8 | `expires_at` | timestamp | | Timestamp when the token expires. |
| 9 | `created_at` | timestamp | | Timestamp when the token was created. |
| 10 | `updated_at` | timestamp | | Timestamp when the token was updated. |

---

## 9. `sessions`
Stores application session data.

| No. | Field Name | Datatype | Constraint | Description |
|-----|------------|----------|------------|-------------|
| 1 | `id` | varchar(255) | PK | Unique identifier for the session. |
| 2 | `user_id` | bigint unsigned | FK | ID of the user associated with the session. |
| 3 | `ip_address` | varchar(45) | | IP address of the user. |
| 4 | `user_agent` | text | | User agent of the browser. |
| 5 | `payload` | longtext | | Session data payload. |
| 6 | `last_activity` | int | | Timestamp of the last activity in the session. |

---

## 10. `tbl_admin_audit`
Logs actions performed by administrators for auditing purposes.

| No. | Field Name | Datatype | Constraint | Description |
|-----|------------|----------|------------|-------------|
| 1 | `id` | bigint unsigned | PK | Unique identifier for the audit entry. |
| 2 | `admin_id` | bigint unsigned | | ID of the admin who performed the action. |
| 3 | `admin_name` | varchar(255) | | Name of the admin at the time of action. |
| 4 | `admin_role` | varchar(255) | | Role of the admin at the time of action. |
| 5 | `action_type` | varchar(255) | | Type of action performed (e.g., UPDATE, DELETE). |
| 6 | `target_id` | varchar(255) | | ID of the target record. |
| 7 | `target_type` | varchar(255) | | Model class name of the target record. |
| 8 | `target_name` | varchar(255) | | Name/Description of the target record. |
| 9 | `old_values` | longtext | | JSON representation of values before the change. |
| 10 | `new_values` | longtext | | JSON representation of values after the change. |
| 11 | `description` | text | | Human-readable description of the action. |
| 12 | `ip_address` | varchar(255) | | IP address from which the action was performed. |
| 13 | `user_agent` | varchar(255) | | User agent used for the action. |
| 14 | `performed_at` | timestamp | | Timestamp when the action was performed. |
| 15 | `created_at` | timestamp | | Record creation timestamp. |
| 16 | `updated_at` | timestamp | | Record update timestamp. |

---

## 11. `tbl_archive`
Stores archived records from other tables.

| No. | Field Name | Datatype | Constraint | Description |
|-----|------------|----------|------------|-------------|
| 1 | `id` | bigint unsigned | PK | Unique identifier for the archive entry. |
| 2 | `table_name` | varchar(50) | | Name of the table from which the data was archived. |
| 3 | `original_id` | bigint unsigned | | Original ID of the record in the source table. |
| 4 | `data` | json | | JSON representation of the archived record. |
| 5 | `archived_by` | bigint unsigned | FK | ID of the user who performed the archiving. |
| 6 | `archived_at` | timestamp | | Timestamp when the record was archived. |

---

## 12. `tbl_category`
Categories for inventory supplies.

| No. | Field Name | Datatype | Constraint | Description |
|-----|------------|----------|------------|-------------|
| 1 | `id` | int unsigned | PK | Unique identifier for the category. |
| 2 | `category_name` | varchar(50) | Unique | Name of the category (e.g., Writing Materials). |

---

## 13. `tbl_notifications`
Stores system notifications for users.

| No. | Field Name | Datatype | Constraint | Description |
|-----|------------|----------|------------|-------------|
| 1 | `id` | bigint unsigned | PK | Unique identifier for the notification. |
| 2 | `user_id` | bigint unsigned | FK | ID of the user who received the notification. |
| 3 | `office_id` | int unsigned | FK | ID of the office associated with the notification. |
| 4 | `request_id` | bigint unsigned | FK | ID of the supply request related to the notification. |
| 5 | `batch_id` | varchar(255) | | Group identifier for batch-related notifications. |
| 6 | `action` | varchar(50) | | Action that triggered the notification. |
| 7 | `message` | text | | Notification message content. |
| 8 | `read_at` | timestamp | | Timestamp when the notification was read. |
| 9 | `created_at` | timestamp | | Creation timestamp. |
| 10 | `updated_at` | timestamp | | Update timestamp. |

---

## 14. `tbl_office`
Offices or departments within the organization.

| No. | Field Name | Datatype | Constraint | Description |
|-----|------------|----------|------------|-------------|
| 1 | `id` | int unsigned | PK | Unique identifier for the office. |
| 2 | `office_name` | varchar(100) | Unique | Name of the office (e.g., Property Custodian). |

---

## 15. `tbl_permissions`
System permissions for role-based access control.

| No. | Field Name | Datatype | Constraint | Description |
|-----|------------|----------|------------|-------------|
| 1 | `id` | bigint unsigned | PK | Unique identifier for the permission. |
| 2 | `name` | varchar(255) | Unique | Unique identifier name for the permission. |
| 3 | `description` | varchar(255) | | Human-readable description of the permission. |

---

## 16. `tbl_request`
Supply requests made by users.

| No. | Field Name | Datatype | Constraint | Description |
|-----|------------|----------|------------|-------------|
| 1 | `id` | bigint unsigned | PK | Unique identifier for the supply request. |
| 2 | `user_id` | bigint unsigned | FK | ID of the user who made the request. |
| 3 | `batch_id` | varchar(255) | | Group identifier for requests made together. |
| 4 | `supply_id` | varchar(50) | FK | Stock number of the requested supply. |
| 5 | `quantity_req` | int | | Quantity of the item requested. |
| 6 | `purpose` | varchar(255) | | Stated purpose for the request. |
| 7 | `status` | enum | | Request status (pending, approved, released, disapproved). |
| 8 | `approved_by` | bigint unsigned | FK | ID of the admin who approved/disapproved the request. |
| 9 | `created_at` | timestamp | | Creation timestamp. |
| 10 | `updated_at` | timestamp | | Update timestamp. |

---

## 17. `tbl_role_permission`
Mapping between roles and permissions (Many-to-Many).

| No. | Field Name | Datatype | Constraint | Description |
|-----|------------|----------|------------|-------------|
| 1 | `role_id` | tinyint unsigned | PK, FK | ID of the role. |
| 2 | `permission_id` | bigint unsigned | PK, FK | ID of the permission. |

---

## 18. `tbl_roles`
User roles within the system.

| No. | Field Name | Datatype | Constraint | Description |
|-----|------------|----------|------------|-------------|
| 1 | `id` | tinyint unsigned | PK | Unique identifier for the role. |
| 2 | `role_name` | varchar(20) | Unique | Name of the role (e.g., admin, superadmin, user). |

---

## 19. `tbl_supply`
Inventory of supplies and items.

| No. | Field Name | Datatype | Constraint | Description |
|-----|------------|----------|------------|-------------|
| 1 | `stock_num` | varchar(50) | PK | Unique stock number assigned to the item. |
| 2 | `item_desc` | text | | Detailed description of the item. |
| 3 | `quantity` | int | | Current quantity available in stock. |
| 4 | `status` | varchar(20) | | Stock status (e.g., Available, Low Stock). |
| 5 | `remarks` | varchar(255) | | Additional notes about the item. |
| 6 | `category_id` | int unsigned | FK | ID of the category the item belongs to. |
| 7 | `unit_id` | int unsigned | FK | ID of the unit of measurement. |
| 8 | `created_at` | timestamp | | Creation timestamp. |
| 9 | `updated_at` | timestamp | | Update timestamp. |
| 10 | `deleted_at` | timestamp | | Soft delete timestamp. |

---

## 20. `tbl_unit`
Units of measurement for supplies.

| No. | Field Name | Datatype | Constraint | Description |
|-----|------------|----------|------------|-------------|
| 1 | `id` | int unsigned | PK | Unique identifier for the unit. |
| 2 | `unit_name` | varchar(20) | Unique | Name of the unit (e.g., pc, pack). |

---

## 21. `tbl_user`
System users.

| No. | Field Name | Datatype | Constraint | Description |
|-----|------------|----------|------------|-------------|
| 1 | `id` | bigint unsigned | PK | Unique identifier for the user. |
| 2 | `first_name` | varchar(50) | | User's first name. |
| 3 | `middle_initial` | char(1) | | User's middle initial. |
| 4 | `last_name` | varchar(50) | | User's last name. |
| 5 | `email` | varchar(191) | Unique | User's email address (login identifier). |
| 6 | `password` | varchar(255) | | Hashed user password. |
| 7 | `role_id` | tinyint unsigned | FK | ID of the user's assigned role. |
| 8 | `has_custom_permissions` | boolean | | Flag indicating if the user has a custom set of permissions (overriding role defaults). |
| 9 | `office_id` | int unsigned | FK | ID of the user's primary office. |
| 10 | `created_at` | timestamp | | Creation timestamp. |
| 11 | `updated_at` | timestamp | | Update timestamp. |
| 12 | `deleted_at` | timestamp | | Soft delete timestamp. |

---

## 22. `tbl_user_permission`
Direct mapping between users and specific permissions (Overrides/Additional).

| No. | Field Name | Datatype | Constraint | Description |
|-----|------------|----------|------------|-------------|
| 1 | `id` | bigint unsigned | PK | Unique identifier for this mapping. |
| 2 | `user_id` | bigint unsigned | FK | ID of the user. |
| 3 | `permission_id` | bigint unsigned | FK | ID of the permission. |

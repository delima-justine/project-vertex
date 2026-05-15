# SMIS Test Matrix

This document outlines the test cases for the Supply Management Information System (SMIS), covering both frontend and backend functionalities.

## 1. Authentication & Session Management
| ID | Test Case | Priority | Expected Result | Verification | Status |
|---|---|---|---|---| :---: |
| AUTH-01 | Login with valid credentials | High | User is redirected to dashboard with valid token. | Manual/Auto | Passed |
| AUTH-02 | Login with invalid credentials | High | Error message displayed: "Invalid email or password." | Manual/Auto | Passed |
| AUTH-03 | Forgot Password request | Medium | Password reset link is sent to the registered email. | Manual | Passed |
| AUTH-04 | Reset Password with valid token | Medium | Password is updated; user can login with new password. | Manual | Pending |
| AUTH-05 | Logout functionality | High | Session is terminated; token is invalidated; redirected to login. | Manual/Auto | Passed |
| AUTH-06 | Token Expiration / Auth Guard | High | Protected routes redirect to login when token is missing/expired. | Manual/Auto | Passed |

## 2. User & Access Management
| ID | Test Case | Priority | Expected Result | Verification | Status |
|---|---|---|---|---| :---: |
| USER-01 | Create new user (Admin) | High | New user record created in `tbl_user` with correct office/role. | Manual/Auto |  |
| USER-02 | Update user details | Medium | User info updated; audit log entry created. | Manual/Auto |  |
| USER-03 | Delete/Archive user | Medium | User record is soft-deleted or archived; login disabled. | Manual |  |
| ROLE-01 | RBAC - Admin Access | High | Admin can access all inventory and request management tools. | Manual |  |
| ROLE-02 | RBAC - Regular User Access | High | User can only see their requests and inventory list (no approval). | Manual |  |
| PERM-01 | Permission Override | Medium | User-specific permission overrides role defaults correctly. | Manual |  |

## 3. Supply & Inventory Management
| ID | Test Case | Priority | Expected Result | Verification | Status |
|---|---|---|---|---| :---: |
| SUP-01 | Add new supply item | High | Item added with stock number, category, and unit. | Manual/Auto | Passed |
| SUP-02 | Update stock quantity | High | Quantity updated correctly; status (Available/Low) reflects change. | Manual/Auto | Passed |
| SUP-03 | Supply Categories CRUD | Medium | Can add/edit/delete categories (e.g., Writing Materials). | Manual | Pending |
| SUP-04 | Supply Units CRUD | Medium | Can add/edit/delete units (e.g., piece, pack). | Manual | Pending |
| SUP-05 | Soft Delete Supply | Medium | Supply removed from view but remains in database (`deleted_at`). | Manual | Pending |

## 4. Supply Request Workflow
| ID | Test Case | Priority | Expected Result | Verification | Status |
|---|---|---|---|---| :---: |
| REQ-01 | Submit new supply request | High | Request created in `pending` status; notification sent to Admin. | Manual/Auto | Passed |
| REQ-02 | Approve supply request | High | Status changes to `approved`; notification sent to User. | Manual/Auto |  |
| REQ-03 | Disapprove supply request | Medium | Status changes to `disapproved`; reason/remark saved. | Manual |  |
| REQ-04 | Release supply request | High | Status changes to `released`; inventory quantity deducted. | Manual/Auto |  |
| REQ-05 | Batch request handling | Medium | Multiple items requested in one batch; same `batch_id`. | Manual | Passed |
| REQ-06 | Edit RIS (Request Issue Slip) | Medium | Approved requests can have RIS details updated before release. | Manual |  |

## 5. Notifications
| ID | Test Case | Priority | Expected Result | Verification | Status |
|---|---|---|---|---| :---: |
| NOTI-01 | Real-time notification receipt | Medium | User receives toast/badge update on status change. | Manual |  |
| NOTI-02 | Mark notification as read | Low | `read_at` timestamp updated; badge count decreases. | Manual |  |
| NOTI-03 | Notification routing | Low | Clicking notification redirects to the specific request. | Manual |  |

## 6. Reports & Auditing
| ID | Test Case | Priority | Expected Result | Verification | Status |
|---|---|---|---|---| :---: |
| AUD-01 | Admin Audit Logging | High | Every CRUD action by admin is logged in `tbl_admin_audit`. | Manual/Auto |  |
| AUD-02 | Archive Data Retrieval | Medium | Archived records can be viewed/restored by superadmin. | Manual |  |
| REP-01 | Generate Inventory Report | Medium | Exported file contains correct current stock levels. | Manual |  |
| REP-02 | Generate Request Summary | Medium | Filtered list of requests for a specific office/date range. | Manual |  |

## 7. Office Management
| ID | Test Case | Priority | Expected Result | Verification | Status |
|---|---|---|---|---| :---: |
| OFC-01 | CRUD Offices | Medium | Add/Edit/Delete offices used for user assignment. | Manual |  |

## 8. Frontend Integration
| ID | Test Case | Priority | Expected Result | Verification | Status |
|---|---|---|---|---| :---: |
| INT-01 | API Interceptor Error Handling | Medium | 401/500 errors handled with appropriate UI feedback. | Manual/Auto | Passed |
| INT-02 | Responsive Layout | Medium | Sidebar/Tables display correctly on mobile/tablet. | Manual | Pending |

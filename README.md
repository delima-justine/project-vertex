# Enhanced Supply Management Information System (ESMIS)

---

## 🔍 System Overview

A web-based Supply Management Information System designed for Polytechnic University of the Philippines - Taguig Campus (PUPT), enabling efficient tracking, requesting, and management of institutional supplies.

---

## ✨ Key Features

### 🔐 Authentication & Security
- Secure login with Gmail-based email validation
- Role-Based Access Control (RBAC) using Spatie Permissions
- Login cooldown on failed attempts
- Forgot password with email reset link
- CSRF protection and session management

### 📊 Admin Dashboard
- At-a-glance supply inventory overview
- Pending requests counter and notifications
- Audit logging for all admin actions
- Real-time notification system with counters

### 📦 Supply Inventory Management
- Add, edit, and delete supply items
- Track stock numbers, descriptions, quantities, and units
- Organize supplies by categories
- Monitor available stock vs. issued quantities

### 📝 Supply Request Workflow
| Step | Description |
|------|-------------|
| **Submit** | Users submit supply requests with quantity and purpose |
| **Review** | Admin reviews pending requests |
| **Approve/Reject** | Admin approves or rejects with remarks |
| **Issue** | Approved requests are issued and tracked |
| **Archive** | Completed requests are archived for records |

### 🔄 Supply Request Journey

| 1️⃣ Submit | 2️⃣ Review | 3️⃣ Decide | 4️⃣ Issue | 5️⃣ Archive |
|-----------|------------|------------|----------|-------------|
| Staff creates a request with quantity and purpose | Admin checks stock availability and request details | Request is approved or rejected with remarks | Approved supplies are released and recorded | Finished transactions are stored for reporting and history |

### 👥 User Management
- Create, edit, and deactivate user accounts
- Assign roles and permissions per user
- Manage office/department affiliations

### 📄 Reports & Records
- Generate supply inventory reports
- Requisition and Issue Slip (RIS) file tracking
- Archived records management
- Request history with full timestamps

### ❓ Help & Support
- User-facing FAQ page
- Admin FAQ for supply management guidance

---

## 👥 Team Vertex

| Aleck Alejandro | Kathleen Citron | Justine Delima | Patricia Quiambao |
| :---: | :---: | :---: | :---: |
| <img src="https://github.com/identicons/aleckalejandro.png" width="150"> | <img src="https://github.com/identicons/kat.png" width="150"> | <img src="https://github.com/identicons/justine.png" width="150"> | <img src="https://github.com/identicons/patricia.png" width="150"> |
| **Developer** | **QA Tester 1** | **Project Manager** | **Documentation Analyst** |

---
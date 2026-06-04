# Enhanced Supply Management Information System (ESMIS)

---

## 🔍 System Overview

A web-based Supply Management Information System designed for Polytechnic University of the Philippines - Taguig Campus (PUPT), enabling efficient tracking, requesting, and management of institutional supplies.

---

## 🚀 Tech Stack

| Frontend | Backend | AI & Infrastructure |
|----------|---------|---------------------|
| ![Angular](https://img.shields.io/badge/Angular-DD0031?style=flat&logo=angular&logoColor=white) | ![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat&logo=laravel&logoColor=white) | ![Gemini](https://img.shields.io/badge/Google_Gemini-8E75B2?style=flat&logo=googlegemini&logoColor=white) |
| ![TypeScript](https://img.shields.io/badge/TypeScript-007ACC?style=flat&logo=typescript&logoColor=white) | ![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white) | ![Hostinger](https://img.shields.io/badge/Hostinger-673DE6?style=flat&logo=hostinger&logoColor=white) |
| ![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=flat&logo=bootstrap&logoColor=white) | ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white) | |
| ![Sass](https://img.shields.io/badge/Sass-CC6699?style=flat&logo=sass&logoColor=white) | ![Pusher](https://img.shields.io/badge/Pusher-302D2D?style=flat&logo=pusher&logoColor=white) | ![PWA](https://img.shields.io/badge/PWA-5A0FC8?style=flat&logo=pwa&logoColor=white) |

---

## 📂 Project Structure

```text
project-vertex/
├── backend/            # Laravel API (smis)
│   ├── app/            # Core logic (Models, Controllers, Services)
│   ├── database/       # Migrations and Seeders
│   └── routes/         # API and Web routes
├── frontend/           # Angular Application (esmis)
│   ├── src/app/        # Components, Services, Models
│   └── src/assets/     # Static assets (images, fonts)
├── PWA_IMPLEMENTATION.md # Detailed PWA Documentation
└── documents/          # Database schemas and documentation
```

---

## ⚙️ Getting Started

### Prerequisites
- PHP 8.2+ & Composer
- Node.js 20+ & npm
- MySQL / MariaDB

### Backend Setup (smis)
1. Navigate to `backend/smis/`
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure your database
4. Run `php artisan key:generate`
5. Run `php artisan migrate --seed`
6. Start the server: `php artisan serve`

### Frontend Setup (esmis)
1. Navigate to `frontend/esmis/`
2. Run `npm install`
3. Start the application: `npm start`
4. Access at `http://localhost:4200`

---

## ✨ Key Features

### 📱 Progressive Web App (PWA)
- **Installable Experience:** Install ESMIS on Desktop and Mobile for a native app feel.
- **Offline Resilience:** Real-time connectivity monitoring with a branded offline overlay to prevent data loss.
- **Smart Updates:** Background version detection with user-friendly "Reload to Update" prompts.

### 🔐 Authentication & Security
- **Granular RBAC:** Role-Based Access Control (Admin, SuperAdmin, User) with Spatie integration.
- **Custom Permissions:** Ability to assign specific permissions to individual users beyond their default roles.
- **Auto-Logout:** Automatic session termination for enhanced security on shared workstations.
- **Secure Recovery:** Secure forgot password workflow with Gmail-based reset links.

### 📦 Advanced Supply Workflow
- **Batch Requisitions:** Users can submit multiple supply items in a single request batch for efficiency.
- **Automated Stock Management:** Real-time stock deduction upon "Released" status and validation for availability.
- **Auto-Disapprove Logic:** Scheduled system cleanup that automatically disapproves unclaimed approved requests after 5 days.
- **Digital RIS Slips:** Automated Requisition and Issue Slips (RIS) sent via email with every status change.

### 📊 Admin & Data Management
- **Comprehensive Audit Trail:** Detailed logging of all system actions (Create, Update, Delete) with before/after data snapshots.
- **Database Governance:** Built-in tools for manual database backups and secure restorations.
- **Automated Backups:** Scheduled database backups to ensure data persistence and disaster recovery.
- **Request Archiving:** Archive completed transactions into JSON-based records for long-term tracking.

### 🔔 Real-time Notifications
- **Live Dashboard Updates:** Real-time status changes and notification counts powered by Pusher.
- **Actionable Alerts:** Instant feedback for users and admins on request approvals, rejections, and releases.

### 🔄 Supply Request Journey

| 1️⃣ Submit | 2️⃣ Review | 3️⃣ Decide | 4️⃣ Issue | 5️⃣ Archive |
|-----------|------------|------------|----------|-------------|
| Staff creates a batch request with quantity and purpose | Admin checks stock availability and request details | Request is approved or rejected with remarks | Approved supplies are released and stock is auto-deducted | Finished transactions are archived for reporting and history |

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
| <img src="https://avatars.githubusercontent.com/u/153336435?v=4" width="150"> | <img src="https://avatars.githubusercontent.com/u/182517661?v=4" width="150"> | <img src="https://avatars.githubusercontent.com/u/182419203?v=4" width="150"> | <img src="https://avatars.githubusercontent.com/u/182516075?v=4" width="150"> |
| **Developer** | **QA Tester** | **Project Manager** | **Documentation Analyst** |

---
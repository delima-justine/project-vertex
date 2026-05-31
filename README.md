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
- **Installable:** Install SMIS on Desktop and Mobile devices for a native app experience.
- **Offline Indicator:** Full-screen "You are Offline" overlay that matches the system branding to prevent data loss during connectivity drops.
- **Auto-Update:** Seamless background version detection with a user-friendly "Reload" prompt.
- **Persistence:** High-priority z-index overlay that preserves application state while waiting for connection.
- *Refer to [PWA_IMPLEMENTATION.md](./PWA_IMPLEMENTATION.md) for technical details.*

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
| <img src="https://avatars.githubusercontent.com/u/153336435?v=4" width="150"> | <img src="https://avatars.githubusercontent.com/u/182517661?v=4" width="150"> | <img src="https://avatars.githubusercontent.com/u/182419203?v=4" width="150"> | <img src="https://avatars.githubusercontent.com/u/182516075?v=4" width="150"> |
| **Developer** | **QA Tester** | **Project Manager** | **Documentation Analyst** |

---
# Enhanced Supply Management Information System (ESMIS)

---

## 🔍 System Overview

A web-based Supply Management Information System designed for Polytechnic University of the Philippines - Taguig Campus (PUPT), enabling efficient tracking, requesting, and management of institutional supplies.

---

## 🚀 Tech Stack

### Frontend
![Angular](https://img.shields.io/badge/angular-%23DD0031.svg?style=for-the-badge&logo=angular&logoColor=white)
![TypeScript](https://img.shields.io/badge/typescript-%23007ACC.svg?style=for-the-badge&logo=typescript&logoColor=white)
![Bootstrap](https://img.shields.io/badge/bootstrap-%238511FA.svg?style=for-the-badge&logo=bootstrap&logoColor=white)
![Sass](https://img.shields.io/badge/Sass-CC6699?style=for-the-badge&logo=sass&logoColor=white)

### Backend
![Laravel](https://img.shields.io/badge/laravel-%23FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/mysql-%2300f.svg?style=for-the-badge&logo=mysql&logoColor=white)
![Pusher](https://img.shields.io/badge/Pusher-302D2D?style=for-the-badge&logo=pusher&logoColor=white)

### AI & Infrastructure
![Gemini](https://img.shields.io/badge/Google%20Gemini-8E75B2?style=for-the-badge&logo=googlegemini&logoColor=white)
![Hostinger](https://img.shields.io/badge/Hostinger-%23673de6.svg?style=for-the-badge&logo=hostinger&logoColor=white)

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
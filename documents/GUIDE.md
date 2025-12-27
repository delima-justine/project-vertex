# Project Setup Guide

This guide provides instructions on how to set up and run the **InternConnect** project.

## Prerequisites

Ensure you have the following installed on your system:
- [PHP](https://www.php.net/downloads.php) (>= 8.2 recommended)
- [Composer](https://getcomposer.org/)
- [Node.js & NPM](https://nodejs.org/)
- A database server (MySQL, PostgreSQL, or SQLite)

---

## 1. Backend Setup (Laravel)

All backend commands should be executed from the `internconnect` directory.

### Install Dependencies
```bash
cd internconnect
composer install
```

### Environment Configuration
1. Copy the example environment file:
   ```bash
   cp .env.example .env
   ```
2. Generate an application key:
   ```bash
   php artisan key:generate
   ```
3. Configure your database settings in the `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

### Database Migrations
Run the migrations to create the necessary tables:
```bash
php artisan migrate
```

### Running the Backend Server
Start the Laravel development server:
```bash
php artisan serve
```
The backend will be available at `http://127.0.0.1:8000`.

---

## 2. Frontend Setup (Vite)

### Install Dependencies
```bash
npm install
```

### Running the Frontend Development Server
Start the Vite development server to compile assets and enable Hot Module Replacement (HMR):
```bash
npm run dev
```
The frontend assets will be served and automatically injected into your Laravel application.

---

## Summary of Commands

| Task | Command |
|------|---------|
| Install PHP Deps | `composer install` |
| Install JS Deps | `npm install` |
| Run Backend | `php artisan serve` |
| Run Frontend | `npm run dev` |
| Run Migrations | `php artisan migrate` |

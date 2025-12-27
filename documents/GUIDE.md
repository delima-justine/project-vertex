# Project Setup Guide

This guide provides instructions on how to set up and run the **InternConnect** project.

## Prerequisites

Ensure you have the following installed on your system:
- [PHP](https://www.php.net/downloads.php) (>= 8.2)
- [Composer](https://getcomposer.org/)
- [Node.js & NPM](https://nodejs.org/)
- A database server (MySQL, PostgreSQL, or SQLite)

---

## Quick Start (Recommended)

The project includes custom scripts to simplify setup and development. All commands should be run from the `internconnect` directory.

### 1. Initial Setup
Run the following command to install dependencies, set up the environment file, generate the app key, and run migrations:
```bash
cd internconnect
composer run setup
```

### 2. Run Everything
To start the backend server, queue listener, logs, and frontend development server (Vite) concurrently:
```bash
composer run dev
```
The application will be available at `http://127.0.0.1:8000`.

---

## Manual Setup

If you prefer to run steps individually:

### Backend Setup
```bash
cd internconnect
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

### Frontend Setup
```bash
cd internconnect
npm install
npm run dev
```

---

## Summary of Custom Commands

| Command | Description |
|---------|-------------|
| `composer run setup` | One-time setup (install deps, env, key, migrate) |
| `composer run dev` | Starts Backend, Frontend, Queue, and Logs simultaneously |
| `composer run test` | Runs the test suite |
| `npm run build` | Compiles frontend assets for production |
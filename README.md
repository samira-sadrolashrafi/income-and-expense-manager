# Tarazino — Personal Finance Management Application

Tarazino is a Persian RTL personal finance management web application built with **PHP** and **CodeIgniter 3**. It helps users track income, expenses, categories, and account balances through a dashboard and dedicated management pages.

## About the Project

The application provides user authentication, financial transaction management, category organization, balance tracking, and account recovery. Its backend follows a **Controller–Service–Model** structure and exposes JSON APIs consumed by the frontend.

The frontend uses **Vue 3** for page interactions, while the dashboard chart remains implemented with JavaScript and Chart.js.

## Features

### Authentication & Account Management

- User registration, login, and logout
- Password hashing and validation
- Remember-me functionality with expiring tokens
- Login attempt throttling
- Password recovery using security questions
- Profile editing, password change, and profile avatar upload
- Account deletion with a 30-day recovery period
- Restore a deleted account during the recovery period

### Dashboard & Balance

- View current account balance
- Manually adjust or reset the balance
- Track balance changes through balance history
- View income, expense, and balance trends in a cash-flow chart
- Filter dashboard reports by week, month, year, or a custom date range

### Categories

- Create, edit, and delete income and expense categories
- Search and filter categories by type
- Sort and paginate category lists
- View deleted categories and restore them within the recovery period
- Prevent duplicate category names and invalid category changes

### Transactions

- Create, edit, and delete income and expense transactions
- Assign transactions to categories
- Store transaction title, amount, date, and optional description
- Automatically update the account balance when transactions are created, edited, or deleted
- Search and filter transactions by type, category, and date range
- Sort and paginate transaction lists
- View deleted transactions and restore eligible records within 30 days

## Tech Stack

- **Backend:** PHP 8.1, CodeIgniter 3
- **Database:** MariaDB
- **Frontend:** HTML5, CSS3, JavaScript, Vue 3
- **CSS Framework:** Bootstrap 5.3 RTL
- **Charts:** Chart.js
- **Date Picker:** Jalali Datepicker
- **Icons:** Font Awesome
- **Environment:** Docker, Docker Compose
- **API Testing:** Postman
- **Database Management:** DBeaver

## Project Structure

```text
income-and-expense-manager/
├── Dockerfile
├── compose.yml
├── php.ini
└── src/
    ├── application/
    │   ├── config/          # Application, database, routes, and migration settings
    │   ├── controllers/     # Page controllers and JSON API endpoints
    │   ├── migrations/      # Versioned database migrations
    │   ├── models/          # Database operations
    │   ├── services/        # Authentication and finance business logic
    │   └── views/           # Authentication, dashboard, categories, transactions, and profile
    ├── assets/
    │   ├── css/
    │   ├── js/              # Shared scripts and dashboard chart
    │   └── vendor/
    ├── system/              # CodeIgniter framework
    └── index.php
```

## Getting Started

### 1. Clone the repository

```bash
git clone https://github.com/samira-sadrolashrafi/income-and-expense-manager.git
cd income-and-expense-manager
```

### 2. Create the Docker network

```bash
docker network create codeigniter_dev_environment_network
```

If the network already exists, this step can be skipped.

### 3. Start the containers

```bash
docker compose up -d --build
```

The application is available at:

```text
http://localhost:8000
```

MariaDB is exposed on port `8100`.

### 4. Run database migrations

```bash
docker compose exec codeigniter-dev-environment php index.php migrate
```

The migrations create the required application tables.

## Notes

- The application uses a Controller–Service–Model structure to separate request handling, business logic, and database operations.
- JSON APIs return a consistent response structure and are used by the Vue-powered pages.
- Income and expense types are defined by categories; transactions use their category to determine their effect on the balance.
- Deleted categories and transactions are soft-deleted and can be restored within 30 days. Restoring a category does not automatically restore its related transactions.
- The dashboard chart is implemented separately with JavaScript and Chart.js.
- The current Vue 3 frontend implementation is available on the `feature/vue-frontend-v2` branch.

## Author

Samira Sadrolashrafi

GitHub: https://github.com/samira-sadrolashrafi

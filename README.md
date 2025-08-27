# Naamu - School Management System

Naamu is a modern, fully-featured School Management System (SMS) built with core PHP and MySQL. This web-based application is designed to serve as the central nervous system for an educational institution, streamlining administrative tasks, academic tracking, and communication for all stakeholders.

## Features

- **Multi-Role Authentication:** Separate modules for Admin, Teacher, and Student.
- **Admin Module:** Manage students, teachers, classes, subjects, fees, and expenses.
- **Teacher Module:** Take attendance, manage grades, and view assigned students.
- **Student Module:** View attendance, grades, and fee payment history.
- **Dynamic Theming:** Switch between five different color schemes.
- **Interactive Tables:** Search, sort, and paginate tables using DataTables.

## Requirements

- PHP 8.0 or higher
- MySQL
- Web server (Apache, Nginx, etc.)

## Installation

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/your-username/naamu-sms.git
    ```
2.  **Create a MySQL database:**
    - Create a new database named `naamu_sms`.
3.  **Configure the application:**
    - Open `config/config.php` and update the database credentials:
      ```php
      define('DB_HOST', 'localhost');
      define('DB_USER', 'your_db_user');
      define('DB_PASS', 'your_db_password');
      define('DB_NAME', 'naamu_sms');
      ```
    - Update the `SITE_URL` to match your local development environment:
      ```php
      define('SITE_URL', 'http://localhost/naamu-sms');
      ```
4.  **Run the installation script:**
    - Open your web browser and navigate to `http://localhost/naamu-sms/install.php`.
    - This will create the necessary tables and a default admin user.
    - **IMPORTANT:** Delete the `install.php` file after the installation is complete for security reasons.

## Usage

- **Admin Login:**
    - Username: `admin`
    - Password: `password`
- After logging in, you can start managing the school system through the admin dashboard.

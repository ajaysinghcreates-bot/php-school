# Installation Guide for Local XAMPP Server

This guide will walk you through the process of setting up and running this project on a local XAMPP server.

## Prerequisites

Before you begin, make sure you have the following software installed on your computer:

*   **XAMPP:** A free and open-source cross-platform web server solution stack package developed by Apache Friends, consisting mainly of the Apache HTTP Server, MariaDB database, and interpreters for scripts written in the PHP and Perl programming languages. You can download it from [https://www.apachefriends.org/index.html](https://www.apachefriends.org/index.html).

## Installation Steps

1.  **Clone the Repository:**

    *   Open a terminal or command prompt.
    *   Navigate to the `htdocs` directory inside your XAMPP installation directory (e.g., `C:/xampp/htdocs` on Windows).
    *   Clone this repository to your `htdocs` directory:

        ```bash
        git clone <repository_url> htdocs
        ```

2.  **Import the Database:**

    *   Start the Apache and MySQL modules in your XAMPP control panel.
    *   Open your web browser and navigate to `http://localhost/phpmyadmin/`.
    *   Create a new database named `naamu_sms`.
    *   Click on the `naamu_sms` database in the left-hand menu.
    *   Click on the "Import" tab.
    *   Click on the "Choose File" button and select the `database.sql` file from the `app/sql` directory of this project.
    *   Click the "Go" button to import the database schema.
    *   Now, click on the "SQL" tab and run the queries from the `migrations.sql` file in the `app/sql` directory to update the database schema.

3.  **Configure the `.env` File:**

    *   In the root directory of the project, you will find a file named `.env`.
    *   This file contains the configuration for the database connection and the site URL.
    *   Make sure the values in this file are correct for your XAMPP setup. The default values should work for a standard XAMPP installation.

4.  **Run the Application:**

    *   Open your web browser and navigate to `http://localhost/htdocs/public/`.
    *   You should see the login page of the application.

## Default Login Credentials

*   **Admin:**
    *   **Username:** admin
    *   **Password:** admin
*   **Teacher:**
    *   **Username:** teacher
    *   **Password:** password
*   **Student:**
    *   **Username:** student
    *   **Password:** password

## File Structure

```
htdocs/
├── app/
│   ├── admin/
│   ├── assets/
│   ├── core/
│   ├── sql/
│   ├── student/
│   └── teacher/
├── assets/
├── config/
├── includes/
├── lib/
└── public/
```

*   **app/:** Contains the core application logic, including the admin, teacher, and student modules.
*   **assets/:** Contains the CSS, JavaScript, and image files.
*   **config/:** Contains the configuration files.
*   **includes/:** Contains the header, footer, and sidebar files.
*   **lib/:** Contains the third-party libraries.
*   **public/:** Contains the public-facing files, such as the login page and the logout script.

## Database Schema

The database schema is defined in the `app/sql/database.sql` file. The migrations are defined in the `app/sql/migrations.sql` file.

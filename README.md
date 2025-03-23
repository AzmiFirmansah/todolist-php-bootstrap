# TodoList PHP Bootstrap

## Description

This repository contains a simple TodoList web application built with PHP, MySQL, and Bootstrap. The project provides a basic task management system where users can add, edit, delete, and manage their tasks efficiently. The purpose of this project is to demonstrate CRUD operations and UI design with Bootstrap.

## Key Features

- **Manage Tasks**: Add, edit, delete, and mark tasks as completed or pending.
- **Due Dates & Priority**: Assign due dates and prioritize tasks (high, medium, low).
- **Search & Filters**: Search tasks and filter by priority or status.
- **Pagination**: Efficient navigation through task lists.
- **User Authentication**: Register, login, logout, and edit profiles.

## Installation Instructions

1. **Download or Clone the Repository**

   - Clone the repository using:
     ```bash
     git clone https://github.com/AzmiFirmansah/todolist-php-bootstrap.git
     ```
   - Alternatively, download the repository as a ZIP file.

2. **Set Up Database**

   - Import `todo.sql` into your MySQL database.

3. **Configure Database Connection**

   - Edit `connection.php` and update the database credentials:
     ```php
     $db_host = 'localhost';
     $db_user = 'root';
     $db_pass = '';
     $db_name = 'todo';
     ```

4. **Run the Application**

   - Deploy the files to a local PHP server (e.g., XAMPP, Laragon, LAMP).
   - Access the application in your browser via `http://localhost/todolist-php-bootstrap/`.

## System Requirements

- **PHP**: 7.4 or higher (Recommended: 8.1+)
- **MySQL**: 8.0.30 or higher (Required)
- **Web Server**: Apache 2.4+ or Nginx
- **Browser**: Chrome, Firefox, Edge (modern versions)


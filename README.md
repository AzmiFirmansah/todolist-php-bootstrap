# TodoList PHP Bootstrap

## Description

This repository contains a simple TodoList web application built with PHP, MySQL, and Bootstrap. The project provides a basic task management system where users can add, edit, delete, and manage their tasks efficiently. The purpose of this project is to demonstrate CRUD operations and responsive UI design with Bootstrap.

## Key Features

- **Task Management**: Add, edit, and delete tasks easily.
- **Due Date Tracking**: Assign due dates to tasks.
- **Status Updates**: Mark tasks as pending or completed.
- **Search Bar**: Quickly search tasks by name.
- **Pagination**: Navigate through multiple pages of tasks efficiently.
- **Priority Filtering**: Filter tasks by priority level (high, medium, or low).
- **Status Filtering**: Filter tasks by status (completed or pending).
- **Responsive Design**: Built with Bootstrap for a mobile-friendly UI.
- **User Authentication**:
  - Register an account to manage tasks.
  - Login and logout functionality.
  - Edit user profile information.

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

## Usage Guide

- **Add Task**: Click the **"Add Task"** button to create a new task.
- **Edit Task**: Click the **"Edit"** button to update an existing task.
- **Delete Task**: Click the **"Delete"** button to remove a task (confirmation required).
- **Search Tasks**: Use the **Search Bar** in the navbar to filter tasks by name. Enter a keyword and click **Search** to see results.
- **Pagination**: Navigate between pages when there are many tasks. The application displays a set number of tasks per page for easier management.
- **Priority Filtering**: Filter tasks by priority level (high, medium, or low) to focus on important tasks.
- **Status Filtering**: Filter tasks by status (completed or pending) to manage workload efficiently.
- **User Authentication**:
  - **Register**: Create an account to manage tasks.
  - **Login**: Access and modify tasks.
  - **Edit Profile**: Update personal information.
  - **Logout**: Securely log out when finished.
- **Task Display**: Tasks are listed with clear status indicators.


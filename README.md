# TodoList PHP Bootstrap

## Description

This repository contains a simple TodoList web application built with PHP, MySQL, and Bootstrap. The project provides a basic task management system where users can add, edit, delete, and manage their tasks efficiently. The purpose of this project is to demonstrate CRUD operations and responsive UI design with Bootstrap.

## Key Features

- **Task Management**: Add, edit, and delete tasks easily.
- **Due Date Tracking**: Assign due dates to tasks.
- **Status Updates**: Mark tasks as pending or completed.
- **Search Bar**: Quickly search tasks by name.
- **Pagination**: Tasks are displayed with pagination, allowing users to navigate through multiple pages of tasks easily.
- **Responsive Design**: Built with Bootstrap for a mobile-friendly UI.
- **User Authentication**:
  - User registration to create an account.
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

- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **Web Server**: Apache or any server supporting PHP
- **Browser**: Chrome, Firefox, Edge, or other modern browsers

## Usage Guide

- Click the **"Add Task"** button to create a new task.
- Use the **"Edit"** button to update an existing task.
- Click the **"Delete"** button to remove a task (confirmation required).
- Use the **Search Bar** in the navbar to filter tasks by name. Enter a keyword and click **Search** to see results.
- Use **Pagination** to navigate between pages when there are many tasks. The application displays a set number of tasks per page, making it easier to manage large lists.
- **User Authentication**:
  - Register an account to manage tasks.
  - Login to access and modify tasks.
  - Edit profile information when needed.
  - Logout securely when finished.
- Tasks will be displayed in a list format with status indicators.


# TaskFlow - Task Management System

A modern, full-stack task management application built with PHP, MySQL, and JavaScript.

## 🚀 Features

- Real-time CRUD operations
- Advanced filtering & search
- Priority-based task organization
- Dark/light theme support
- Analytics dashboard
- Responsive design

## 🛠 Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Charts**: Chart.js
- **Icons**: Font Awesome

## ⚡ Quick Start

1. **Set up database**
   ```sql
   CREATE DATABASE task_manager;
   USE task_manager;
   -- Import database_setup.sql
   ```

2. **Configure connection**
   ```php
   // Update includes/config.php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'task_manager');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

3. **Access application**
   - Main: `http://localhost/project3-task-manager/`
   - Analytics: `http://localhost/project3-task-manager/analytics.php`

## 📁 Project Structure

```
project3-task-manager/
├── index.php              # Main dashboard
├── analytics.php          # Analytics page
├── api/tasks.php          # REST API endpoints
├── includes/              # PHP classes & config
├── css/style.css          # Stylesheets
└── js/core.js            # Application logic
```

## 🔧 API Endpoints

- `GET /api/tasks.php` - Fetch tasks
- `POST /api/tasks.php` - Create task
- `PUT /api/tasks.php` - Update task
- `DELETE /api/tasks.php` - Delete task

---

## 📄 License

MIT License

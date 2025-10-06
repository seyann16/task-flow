-- Create database
CREATE DATABASE task_manager;
USE task_manager;

-- Tasks table with comprehensive fields
CREATE TABLE tasks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('pending', 'completed') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    due_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted BOOLEAN DEFAULT FALSE
);

-- Sample data
INSERT INTO tasks (title, description, priority, due_date) VALUES
('Learn PHP', 'Complete Project 3 of the learning path', 'high', CURDATE() + INTERVAL 7 DAY),
('Design database schema', 'Create efficient database structure', 'medium', CURDATE() + INTERVAL 3 DAY),
('Implement AJAX features', 'Add real-time updates to task list', 'high', CURDATE() + INTERVAL 5 DAY);
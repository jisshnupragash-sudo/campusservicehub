CREATE DATABASE IF NOT EXISTS campus_service_hub;
USE campus_service_hub;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_services_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO users (name, email, password, role)
VALUES
('Admin User', 'admin@example.com', '$2y$10$w0YDL9g4i0dO6Z0Mm3gyUOQYvPCAULJNNPJBWlAbIYW/W2PASi6ei', 'admin'),
('Student User', 'user@example.com', '$2y$10$w0YDL9g4i0dO6Z0Mm3gyUOQYvPCAULJNNPJBWlAbIYW/W2PASi6ei', 'user');
-- Password for both demo accounts: password123

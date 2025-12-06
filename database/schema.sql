-- Create Database
CREATE DATABASE IF NOT EXISTS onlinecourse CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE onlinecourse;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(255) NOT NULL,
    role INT NOT NULL DEFAULT 0 COMMENT '0: học viên, 1: giảng viên, 2: quản trị viên',
    status INT NOT NULL DEFAULT 1 COMMENT '0: inactive, 1: active',
    avatar VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    instructor_id INT NOT NULL,
    category_id INT NOT NULL,
    price DECIMAL(10,2) DEFAULT 0.00,
    duration_weeks INT DEFAULT 1,
    level VARCHAR(50) DEFAULT 'Beginner' COMMENT 'Beginner, Intermediate, Advanced',
    image VARCHAR(255) DEFAULT NULL,
    status VARCHAR(50) DEFAULT 'pending' COMMENT 'pending, approved, rejected',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_instructor (instructor_id),
    INDEX idx_category (category_id),
    INDEX idx_level (level),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create enrollments table
CREATE TABLE IF NOT EXISTS enrollments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    student_id INT NOT NULL,
    enrolled_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'active' COMMENT 'active, completed, dropped',
    progress INT DEFAULT 0 COMMENT 'phần trăm hoàn thành (0-100)',
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (course_id, student_id),
    INDEX idx_student (student_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create lessons table
CREATE TABLE IF NOT EXISTS lessons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT,
    video_url VARCHAR(255) DEFAULT NULL,
    `order` INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    INDEX idx_course (course_id),
    INDEX idx_order (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create materials table
CREATE TABLE IF NOT EXISTS materials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lesson_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) COMMENT 'pdf, doc, ppt, v.v.',
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    INDEX idx_lesson (lesson_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data

-- Insert admin user (password: admin123)
INSERT INTO users (username, email, password, fullname, role) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 2),
('instructor1', 'instructor1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn A', 1),
('student1', 'student1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị B', 0);

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Lập trình Web', 'Các khóa học về phát triển web frontend và backend'),
('Lập trình Mobile', 'Các khóa học về phát triển ứng dụng di động'),
('Cơ sở dữ liệu', 'Các khóa học về quản trị và thiết kế CSDL'),
('Trí tuệ nhân tạo', 'Các khóa học về AI và Machine Learning'),
('DevOps', 'Các khóa học về CI/CD, Docker, Kubernetes');

-- Insert sample courses
INSERT INTO courses (title, description, instructor_id, category_id, price, duration_weeks, level, status) VALUES
('PHP cơ bản đến nâng cao', 'Khóa học PHP từ cơ bản đến nâng cao, bao gồm OOP, MVC, và các framework phổ biến.', 2, 1, 500000.00, 8, 'Beginner', 'approved'),
('JavaScript ES6+', 'Học JavaScript hiện đại với ES6+ features, async/await, và các kỹ thuật lập trình nâng cao.', 2, 1, 600000.00, 6, 'Intermediate', 'approved'),
('MySQL từ A-Z', 'Thiết kế và quản trị cơ sở dữ liệu MySQL cho người mới bắt đầu.', 2, 3, 400000.00, 4, 'Beginner', 'approved');

-- Add more users (2 instructors, 6 students). Passwords reuse existing hash (placeholder).
INSERT INTO users (username, email, password, fullname, role) VALUES
('instructor2', 'instructor2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Thị C', 1),
('instructor3', 'instructor3@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Văn D', 1),
('student2', 'student2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ngô Thị E', 0),
('student3', 'student3@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bùi Văn F', 0),
('student4', 'student4@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hoàng Thị G', 0),
('student5', 'student5@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trịnh Văn H', 0),
('student6', 'student6@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lý Thị I', 0),
('student7', 'student7@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Đỗ Văn K', 0);

-- Add new categories
INSERT INTO categories (name, description) VALUES
('Data Science', 'Khóa học về xử lý dữ liệu, phân tích và trực quan hóa'),
('Cybersecurity', 'Bảo mật ứng dụng, mạng và best practices');

-- Add more courses (instructor ids assume existing seeds + new users above)
INSERT INTO courses (title, description, instructor_id, category_id, price, duration_weeks, level, status) VALUES
('React & Redux', 'Xây dựng SPA hiện đại với React, quản lý state bằng Redux và best practices.', 4, 1, 700000.00, 6, 'Intermediate', 'approved'),
('Android Kotlin', 'Phát triển ứng dụng Android bằng Kotlin, architecture và triển khai.', 5, 2, 650000.00, 6, 'Intermediate', 'approved'),
('Data Science with Python', 'Nhập môn Data Science: pandas, numpy, visualizations và pipeline cơ bản.', 4, 6, 1200000.00, 10, 'Beginner', 'approved'),
('Machine Learning Advanced', 'Các thuật toán ML nâng cao và kỹ thuật tối ưu hóa mô hình.', 4, 4, 1500000.00, 12, 'Advanced', 'pending'),
('Cybersecurity Fundamentals', 'Nguyên lý bảo mật, OWASP, secure coding và defensive measures.', 5, 7, 800000.00, 5, 'Beginner', 'approved');

-- Add enrollments (students enrolling in various courses)
INSERT INTO enrollments (course_id, student_id, status, progress) VALUES
(1, 6, 'active', 10),   -- student2 in PHP course
(1, 7, 'completed', 100), -- student3 completed PHP
(2, 6, 'active', 40),   -- student2 in JS
(4, 8, 'active', 5),    -- student4 in React
(4, 9, 'active', 20),   -- student5 in React
(5, 10, 'active', 0),   -- student6 in Android
(6, 11, 'active', 15),  -- student7 in Data Science
(3, 6, 'dropped', 0),   -- student2 dropped MySQL
(7, 7, 'active', 30),   -- student3 in Machine Learning (pending course)
(8, 9, 'active', 2);    -- student5 in Cybersecurity

-- Add lessons for the new courses (orders approximate)
-- React & Redux (course_id = 4)
INSERT INTO lessons (course_id, title, content, video_url, `order`) VALUES
(4, 'Intro to React', 'Overview of React, JSX and component model.', 'https://example.com/videos/react1.mp4', 1),
(4, 'State and Props', 'Managing state and props in functional and class components.', 'https://example.com/videos/react2.mp4', 2),
(4, 'Redux Fundamentals', 'Actions, reducers, store, and middleware.', 'https://example.com/videos/react3.mp4', 3),
(4, 'Advanced Patterns', 'Hooks, performance optimizations and testing.', 'https://example.com/videos/react4.mp4', 4);

-- Android Kotlin (course_id = 5)
INSERT INTO lessons (course_id, title, content, video_url, `order`) VALUES
(5, 'Kotlin Basics', 'Syntax, types, and basic constructs.', 'https://example.com/videos/kotlin1.mp4', 1),
(5, 'Android Architecture', 'Activities, fragments, and navigation.', 'https://example.com/videos/kotlin2.mp4', 2),
(5, 'Working with REST APIs', 'HTTP, Retrofit and data parsing.', 'https://example.com/videos/kotlin3.mp4', 3);

-- Data Science with Python (course_id = 6)
INSERT INTO lessons (course_id, title, content, video_url, `order`) VALUES
(6, 'Python for Data', 'Numpy and pandas basics.', 'https://example.com/videos/ds1.mp4', 1),
(6, 'Data Cleaning', 'Handling missing data and transformations.', 'https://example.com/videos/ds2.mp4', 2),
(6, 'Visualization', 'Matplotlib and Seaborn examples.', 'https://example.com/videos/ds3.mp4', 3),
(6, 'Feature Engineering', 'Creating useful features for models.', 'https://example.com/videos/ds4.mp4', 4),
(6, 'Model Evaluation', 'Metrics and validation techniques.', 'https://example.com/videos/ds5.mp4', 5);

-- Machine Learning Advanced (course_id = 7)
INSERT INTO lessons (course_id, title, content, video_url, `order`) VALUES
(7, 'Advanced Regression', 'Regularization and ensemble methods.', 'https://example.com/videos/ml1.mp4', 1),
(7, 'Neural Networks', 'Deep learning fundamentals.', 'https://example.com/videos/ml2.mp4', 2),
(7, 'Model Deployment', 'Serving models and CI/CD for ML.', 'https://example.com/videos/ml3.mp4', 3),
(7, 'Optimization Tricks', 'Hyperparameter tuning and speedups.', 'https://example.com/videos/ml4.mp4', 4);

-- Cybersecurity Fundamentals (course_id = 8)
INSERT INTO lessons (course_id, title, content, video_url, `order`) VALUES
(8, 'Security Basics', 'CIA triad and threat models.', 'https://example.com/videos/sec1.mp4', 1),
(8, 'OWASP Top 10', 'Common web vulnerabilities and fixes.', 'https://example.com/videos/sec2.mp4', 2),
(8, 'Secure Coding', 'Practical guidelines and examples.', 'https://example.com/videos/sec3.mp4', 3);

-- Add materials linked to several lessons (lesson ids assigned sequentially starting from first lesson inserted above)
INSERT INTO materials (lesson_id, filename, file_path, file_type) VALUES
(1, 'react-intro.pdf', '/materials/react/react-intro.pdf', 'pdf'),
(2, 'react-state.pdf', '/materials/react/react-state.pdf', 'pdf'),
(3, 'redux-cheatsheet.pdf', '/materials/react/redux-cheatsheet.pdf', 'pdf'),
(5, 'kotlin-basics.pdf', '/materials/kotlin/kotlin-basics.pdf', 'pdf'),
(8, 'ds-pandas-cheatsheet.pdf', '/materials/datascience/pandas-cheatsheet.pdf', 'pdf'),
(10, 'ds-feature-engineering.pdf', '/materials/datascience/feature-engineering.pdf', 'pdf'),
(13, 'ml-advanced.pdf', '/materials/ml/ml-advanced.pdf', 'pdf'),
(17, 'security-owasp.pdf', '/materials/security/owasp-top10.pdf', 'pdf');

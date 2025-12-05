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


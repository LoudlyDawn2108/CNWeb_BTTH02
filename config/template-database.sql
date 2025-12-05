-- Tạo cơ sở dữ liệu (nếu chưa có)
CREATE DATABASE IF NOT EXISTS onlinecourse CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE onlinecourse;

-- 1. Bảng users (Tạo trước vì được bảng courses và enrollments tham chiếu)
CREATE TABLE users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       username VARCHAR(255) NOT NULL UNIQUE,
                       email VARCHAR(255) NOT NULL UNIQUE,
                       password VARCHAR(255) NOT NULL,
                       fullname VARCHAR(255),
                       role INT DEFAULT 0 COMMENT '0: học viên, 1: giảng viên, 2: quản trị viên',
                       created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Bảng categories (Tạo trước vì được bảng courses tham chiếu)
CREATE TABLE categories (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            name VARCHAR(255) NOT NULL,
                            description TEXT,
                            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Bảng courses (Tham chiếu users và categories)
CREATE TABLE courses (
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         title VARCHAR(255) NOT NULL,
                         description TEXT,
                         instructor_id INT NOT NULL,
                         category_id INT,
                         price DECIMAL(10, 2) DEFAULT 0.00,
                         duration_weeks INT,
                         level VARCHAR(50) COMMENT 'Beginner, Intermediate, Advanced',
                         image VARCHAR(255),
                         created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                         updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Khóa ngoại
                         FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE CASCADE,
                         FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 4. Bảng enrollments (Tham chiếu courses và users)
CREATE TABLE enrollments (
                             id INT AUTO_INCREMENT PRIMARY KEY,
                             course_id INT NOT NULL,
                             student_id INT NOT NULL,
                             enrolled_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                             status VARCHAR(50) DEFAULT 'active' COMMENT 'active, completed, dropped',
                             progress INT DEFAULT 0 COMMENT 'Phần trăm hoàn thành 0-100',

    -- Khóa ngoại
                             FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
                             FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Bảng lessons (Tham chiếu courses)
CREATE TABLE lessons (
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         course_id INT NOT NULL,
                         title VARCHAR(255) NOT NULL,
                         content LONGTEXT,
                         video_url VARCHAR(255),
                         `order` INT DEFAULT 0, -- Dùng dấu huyền vì order là từ khóa của SQL
                         created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    -- Khóa ngoại
                         FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Bảng materials (Tham chiếu lessons)
CREATE TABLE materials (
                           id INT AUTO_INCREMENT PRIMARY KEY,
                           lesson_id INT NOT NULL,
                           filename VARCHAR(255) NOT NULL,
                           file_path VARCHAR(255) NOT NULL,
                           file_type VARCHAR(50) COMMENT 'pdf, doc, ppt, v.v.',
                           uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    -- Khóa ngoại
                           FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
) ENGINE=InnoDB;
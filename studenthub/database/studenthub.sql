-- ==========================================================
-- ITUE203: Web Development Frameworks - Practical 15
-- STUDENT HUB: UNIFIED DATABASE SCHEMA & SEED DATA
-- Database Name: studenthub_db
-- ==========================================================

CREATE DATABASE IF NOT EXISTS studenthub_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE studenthub_db;

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------
-- 1. Roles Table (RBAC System - P10, P14)
-- ----------------------------------------------------------
DROP TABLE IF EXISTS roles;
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- 2. Users Table (Authentication Credentials - P9, P10, P14)
-- ----------------------------------------------------------
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id) ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- 3. Students Table (Academic Profiles - P2, P8, P9, P11)
-- ----------------------------------------------------------
DROP TABLE IF EXISTS students;
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL UNIQUE,
    student_id VARCHAR(50) NOT NULL UNIQUE,
    student_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NULL,
    course VARCHAR(100) NOT NULL,
    branch VARCHAR(100) NOT NULL,
    academic_year VARCHAR(30) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    address TEXT NULL,
    pincode VARCHAR(10) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_students_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- 4. Events Table (Campus Events - P8, P12, P13, P14)
-- ----------------------------------------------------------
DROP TABLE IF EXISTS events;
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    category VARCHAR(50) DEFAULT 'Academic',
    event_date DATE NOT NULL,
    event_time VARCHAR(20) DEFAULT '10:00 AM',
    location VARCHAR(150) NOT NULL,
    max_seats INT DEFAULT 100,
    poster_image VARCHAR(255) NULL,
    status ENUM('upcoming', 'completed', 'cancelled') DEFAULT 'upcoming',
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_events_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- 5. Registrations Table (Event Enrollments - P8, P13, P14)
-- ----------------------------------------------------------
DROP TABLE IF EXISTS registrations;
CREATE TABLE registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('registered', 'approved', 'rejected') DEFAULT 'registered',
    notes VARCHAR(255) NULL,
    UNIQUE KEY uq_student_event (student_id, event_id),
    CONSTRAINT fk_reg_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT fk_reg_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- 6. Notices Table (Announcements - P2, P14)
-- ----------------------------------------------------------
DROP TABLE IF EXISTS notices;
CREATE TABLE notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    category ENUM('Academic', 'Exam', 'Event', 'General') DEFAULT 'General',
    content TEXT NOT NULL,
    priority ENUM('normal', 'high', 'urgent') DEFAULT 'normal',
    published_date DATE NOT NULL,
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notices_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- 7. Inquiries Table (Contact Submissions - P7)
-- ----------------------------------------------------------
DROP TABLE IF EXISTS inquiries;
CREATE TABLE inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    mobile VARCHAR(20) NOT NULL,
    pincode VARCHAR(10) NOT NULL,
    gender VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'in_progress', 'resolved') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- 8. Attendance Table (Academic Records - P2)
-- ----------------------------------------------------------
DROP TABLE IF EXISTS attendance;
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject VARCHAR(100) NOT NULL,
    total_classes INT NOT NULL DEFAULT 40,
    classes_attended INT NOT NULL DEFAULT 35,
    semester VARCHAR(20) DEFAULT 'Semester 3',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_att_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- 9. Academic Results Table (Grades - P2)
-- ----------------------------------------------------------
DROP TABLE IF EXISTS academic_results;
CREATE TABLE academic_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject VARCHAR(100) NOT NULL,
    marks_obtained INT NOT NULL,
    max_marks INT NOT NULL DEFAULT 100,
    grade VARCHAR(5) NOT NULL,
    semester VARCHAR(20) DEFAULT 'Semester 3',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_res_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- 10. Audit Logs Table (Administrative Logging - P14)
-- ----------------------------------------------------------
DROP TABLE IF EXISTS audit_logs;
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT NULL,
    ip_address VARCHAR(45) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- ==========================================================
-- SEED DATA
-- Default Passwords:
-- Admin: admin123
-- Student: student123
-- Moderator: mod123
-- Hashes generated via password_hash(PASSWORD_BCRYPT)
-- ==========================================================

-- Insert Roles
INSERT INTO roles (id, role_name, description) VALUES
(1, 'admin', 'Administrator with full system privileges and audit log access'),
(2, 'student', 'Enrolled student with personal portal and registration access'),
(3, 'moderator', 'Staff member managing campus events and notices');

-- Insert Pre-configured Users
INSERT INTO users (id, name, email, password, role_id, status) VALUES
(1, 'System Administrator', 'admin@studenthub.local', '$2y$10$1GKZa7Z4mhhffPbYCkKEVuVW7n5r6Dre9aWDk.s7CO30bi9Cc6bAC', 1, 'active'),
(2, 'Meet Tanti', 'meet@studenthub.local', '$2y$10$b7X.PMEDIhmksjKtkztB2uFVOZiMItCqzdMXlC38HRD2h9QJYDL5C', 2, 'active'),
(3, 'Dhara Patel', 'dhara@studenthub.local', '$2y$10$b7X.PMEDIhmksjKtkztB2uFVOZiMItCqzdMXlC38HRD2h9QJYDL5C', 2, 'active'),
(4, 'Prof. Sharma (Moderator)', 'moderator@studenthub.local', '$2y$10$7uy6KwHs0YSU0BFFNSUOruYlh5MggVRkQSYdpP7cWA6HRizV0Vgcq', 3, 'active');

-- Insert Student Profiles (Linked to Users & P2 Data)
INSERT INTO students (id, user_id, student_id, student_name, phone, course, branch, academic_year, gender, address, pincode) VALUES
(1, 2, '25CS104', 'Meet Tanti', '9876543210', 'B.Tech', 'Computer Science & Engineering', 'Second Year', 'Female', 'CSPIT Campus, Changa', '388421'),
(2, 3, '25CS105', 'Dhara Patel', '9876543211', 'B.Tech', 'Information Technology', 'Second Year', 'Female', 'CSPIT Campus, Changa', '388421'),
(3, NULL, '25CS106', 'Aarav Shah', '9876543212', 'B.Tech', 'Computer Engineering', 'First Year', 'Male', 'Anand, Gujarat', '388001');

-- Insert Events (From P8, P12, P14)
INSERT INTO events (id, event_name, description, category, event_date, event_time, location, max_seats, poster_image, status, created_by) VALUES
(1, 'National Hackathon 2026', '24-hour inter-college coding competition and innovative product building.', 'Technical', '2026-11-15', '09:00 AM', 'Central Computing Lab, CSPIT', 120, 'hackathon.jpg', 'upcoming', 1),
(2, 'Web Development Workshop', 'Hands-on practical workshop covering modern PHP, MySQL PDO, and Bootstrap 5.', 'Workshop', '2026-11-20', '02:00 PM', 'Seminar Hall 3', 80, 'webdev.jpg', 'upcoming', 1),
(3, 'Annual Project Exhibition', 'Demonstration of capstone web applications and industry-ready frameworks.', 'Exhibition', '2026-12-05', '10:00 AM', 'University Auditorium', 200, 'project_expo.jpg', 'upcoming', 1),
(4, 'Cyber Security Symposium', 'Industry expert talks on ethical hacking, zero-trust architectures, and secure coding.', 'Seminar', '2026-12-12', '11:00 AM', 'Auditorium Hall B', 150, NULL, 'upcoming', 1);

-- Insert Registrations (P8, P14)
INSERT INTO registrations (id, student_id, event_id, registration_date, status, notes) VALUES
(1, 1, 1, '2026-10-25 10:30:00', 'approved', 'Confirmed participant team lead'),
(2, 2, 1, '2026-10-25 11:15:00', 'approved', 'Team member'),
(3, 1, 2, '2026-10-26 14:00:00', 'registered', 'Awaiting seat confirmation'),
(4, 3, 3, '2026-10-27 09:45:00', 'registered', 'Hardware requirement submitted');

-- Insert Campus Notices (P2)
INSERT INTO notices (id, title, category, content, priority, published_date, created_by) VALUES
(1, 'Mid-Semester Examination Schedule Announced', 'Exam', 'The Mid-Semester theory and practical exams for B.Tech Semester 3 will commence from November 10, 2026. Hall tickets can be downloaded from the student portal.', 'high', '2026-10-28', 1),
(2, 'Practical 15 Project Submission Deadline', 'Academic', 'All students must submit their complete integrated Practical 15 Student Hub portal with database SQL and test report by October 30, 2026.', 'urgent', '2026-10-29', 1),
(3, 'Campus Placement Orientation Drive', 'General', 'Training and Placement Cell is organizing an orientation webinar on resume building and technical interviews this Friday at 4 PM.', 'normal', '2026-10-27', 1);

-- Insert Attendance (P2 Specific Data)
INSERT INTO attendance (id, student_id, subject, total_classes, classes_attended, semester) VALUES
(1, 1, 'Java Programming', 40, 36, 'Semester 3'),
(2, 1, 'HTML & Web Frameworks', 38, 34, 'Semester 3'),
(3, 1, 'Database Management Systems', 42, 35, 'Semester 3'),
(4, 2, 'Java Programming', 40, 38, 'Semester 3'),
(5, 2, 'HTML & Web Frameworks', 38, 37, 'Semester 3'),
(6, 2, 'Database Management Systems', 42, 39, 'Semester 3');

-- Insert Academic Results (P2 Specific Data)
INSERT INTO academic_results (id, student_id, subject, marks_obtained, max_marks, grade, semester) VALUES
(1, 1, 'Java Programming', 84, 100, 'AA', 'Semester 3'),
(2, 1, 'HTML & Web Frameworks', 91, 100, 'AA', 'Semester 3'),
(3, 1, 'Database Management Systems', 86, 100, 'AB', 'Semester 3'),
(4, 2, 'Java Programming', 88, 100, 'AA', 'Semester 3'),
(5, 2, 'HTML & Web Frameworks', 95, 100, 'AA', 'Semester 3'),
(6, 2, 'Database Management Systems', 90, 100, 'AA', 'Semester 3');

-- Insert Initial Inquiries (P7)
INSERT INTO inquiries (id, name, email, mobile, pincode, gender, address, message, status) VALUES
(1, 'Rohan Verma', 'rohan@example.com', '9898989898', '388001', 'Male', 'Anand, Gujarat', 'Inquiry regarding admission transfer and course syllabus.', 'resolved'),
(2, 'Ananya Mehta', 'ananya@example.com', '9871234567', '380015', 'Female', 'Ahmedabad, Gujarat', 'Requesting guidance on attending the upcoming Hackathon.', 'new');

-- Insert Audit Logs (P14)
INSERT INTO audit_logs (id, user_id, action, details, ip_address, created_at) VALUES
(1, 1, 'SYSTEM_INIT', 'Student Hub practical 15 database initialized with schema and seed data', '127.0.0.1', '2026-10-30 10:00:00'),
(2, 1, 'ADMIN_LOGIN', 'Administrator logged into Admin Control Centre', '127.0.0.1', '2026-10-30 10:05:00'),
(3, 1, 'EVENT_CREATE', 'Added new event: National Hackathon 2026', '127.0.0.1', '2026-10-30 10:06:00');

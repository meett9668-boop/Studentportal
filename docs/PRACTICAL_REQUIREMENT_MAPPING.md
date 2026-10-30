# ITUE203 Web Development Frameworks - Practical Requirement Mapping

This document provides complete traceability between the 14 individual laboratory practicals (Practicals 1–14) and their integrated implementation in the **Student Hub** unified web application for **Practical 15**.

---

## 1. Traceability Matrix

| Practical | Official Title | Main Concepts & Technologies | Required Functionality | Student Hub Module / Integration | Testing Method |
|:---:|:---|:---|:---|:---|:---|
| **P1** | Introduction to Web Development & Project Structure | Directory structure, Sitemap, Git versioning, HTML foundation | Website layout blueprint, information architecture, navigation sitemap | System sitemap in Footer / About page, structured folder organization (`/studenthub`) | Inspection of sitemap link and directory hierarchy |
| **P2** | Semantic HTML5 & Multi-page Portal Design | Semantic HTML (`<header>`, `<nav>`, `<main>`, `<section>`, `<table>`), multi-page layout | Responsive navigation, attendance table, assignment lists, results display, notice boards | Public & Portal templates: `index.php`, `academics.php`, `notices.php`, `events.php`, `contact.php`, `student/attendance.php`, `student/results.php` | Visual verification of semantic elements and HTML5 table rendering |
| **P3** | Modern Layouts with CSS Grid, Flexbox & Bootstrap | CSS Grid, Flexbox, Bootstrap 5.3, responsive layouts, cards, sticky headers | Responsive grid systems, flex navbars, card grids for courses & events | Common design system: `assets/css/style.css`, Bootstrap 5 navbar, grid containers across all pages | Responsive viewport resize testing (Mobile, Tablet, Desktop) |
| **P4** | Client-side Interactivity with JavaScript DOM | DOM manipulation, event listeners, dynamic style toggles, hamburger menu, FAQ accordion | Interactive mobile drawer, dynamic FAQ tabs, dark/light theme toggle, toast notifications | `assets/js/app.js`: dynamic mobile drawer toggle, live DOM updates, accessible alerts | Interactive click testing on UI toggles and DOM state changes |
| **P5** | Client-side Form Validation & UX Feedback | Regular Expressions, HTML5 constraints, real-time input validation, error states | Real-time student registration & contact form validation (email format, student ID pattern, password length) | `register.php`, `contact.php`, `assets/js/app.js`: inline input validation & feedback badges | Client-side validation test cases with valid/invalid patterns |
| **P6** | Rendering External JSON Data with Fetch API | Fetch API, asynchronous JavaScript (`async/await`), JSON parsing, DOM injection | Async retrieval of `data/faqs.json`, search filter, category filter, sorting, client pagination, error handling | `support.php` (Help & FAQ Center): live dynamic FAQ filtering, category pill selection, pagination controls | Async fetch verification with network interception & mock failure tests |
| **P7** | Server-side Form Processing & Input Sanitization | PHP `$_POST`, `filter_var()`, `htmlspecialchars()`, file logging (CSV/JSON) | Contact form submission handler, server-side sanitization, persistent message recording | `contact.php`: server sanitization, MySQL `inquiries` table insertion + fallback audit logging | Form submission testing with XSS payloads and empty values |
| **P8** | Relational Database Design & PDO Connection | MySQL, 3NF Normalization, Foreign Keys, PDO with Prepared Statements | Relational database schema with constraints, centralized PDO database connection class | `config/db.php`, `database/studenthub.sql`: normalized schema (`users`, `roles`, `students`, `events`, `registrations`, `audit_logs`, `notices`, `inquiries`) | PDO connection test script & foreign key constraint validation |
| **P9** | Secure Student Registration with Password Hashing | `password_hash(PASSWORD_BCRYPT)`, duplicate checking, database transactions | Secure multi-table registration inserting into `users` and `students`, duplicate email & student ID rejection | `register.php`: atomic transaction creating user login credentials and linked student record | Test registration with unique vs duplicate email/student ID |
| **P10** | Session-based Authentication & Role Redirection | PHP Sessions, `session_regenerate_id()`, `password_verify()`, RBAC routing, timeout | Login with credentials verification, session fixation prevention, role-based redirection, secure logout | `login.php`, `logout.php`, `includes/auth.php`: session gatekeeper and login redirection | Successful authentication, invalid password tests, session logout verification |
| **P11** | Student Record Management (CRUD) | MySQL CRUD, Prepared Statements, Search & Filter queries, Pagination | Admin student directory: list students, search by ID/name, filter by branch/year, edit details, safe deletion | `admin/students.php`: full administrative student management interface with modal editing | Test Create, Read, Update, Delete with database persistence |
| **P12** | Event Management & Secure File Uploads | Multipart form handling, MIME-type validation, unique filenames, poster storage | Admin event creator: add/edit/delete campus events, upload poster images safely to `/uploads/events/` | `admin/events.php`: event management with image upload validation and public display | Upload valid JPG/PNG vs disallowed file types, verify storage path |
| **P13** | RESTful JSON API with Asynchronous CRUD | PHP JSON endpoints (`header('Content-Type: application/json')`), HTTP methods, Fetch API | Asynchronous API for events and student registrations without full page reload | `api/events.php`, `api/registrations.php`: JSON API endpoints with fetch integration | Async API testing with `fetch()` calls in browser console and network tab |
| **P14** | Admin Dashboard with Role-Based Access Control & Audit Logging | PDO aggregate queries (`COUNT`), JOIN queries, RBAC access guards, administrative audit trail | Comprehensive administrative dashboard with live KPI counters, latest registrations table, searchable audit logs | `admin/dashboard.php`, `admin/audit_logs.php`, `includes/audit.php`: access restriction & activity tracking | Role verification tests (Admin vs Student access attempt), audit log inspection |

---

## 2. Shared Data Entities & Schema Plan

All practicals will operate seamlessly over a single centralized MySQL database: **`studenthub_db`**.

- **`roles`**: System roles (`admin`, `moderator`, `student`).
- **`users`**: Authentication credentials (`id`, `role_id`, `email`, `password_hash`, `is_active`, `created_at`).
- **`students`**: Academic profile (`id`, `user_id`, `student_id`, `full_name`, `phone`, `course`, `branch`, `academic_year`, `gender`, `created_at`).
- **`events`**: Campus events (`id`, `title`, `description`, `event_date`, `location`, `poster_image`, `max_seats`, `status`, `created_by`, `created_at`).
- **`registrations`**: Event registrations (`id`, `event_id`, `student_id`, `registration_date`, `status`, `notes`).
- **`notices`**: Academic & campus announcements (`id`, `title`, `category`, `content`, `priority`, `published_at`, `created_by`).
- **`inquiries`**: Contact form submissions (`id`, `name`, `email`, `subject`, `message`, `status`, `created_at`).
- **`audit_logs`**: Administrative audit trail (`id`, `user_id`, `action`, `details`, `ip_address`, `created_at`).

---

## 3. Completion Conditions & Verification
- [x] All 14 practicals analyzed in depth from the student's lab work.
- [x] Clear mapping between each practical concept and its concrete implementation in Student Hub.
- [x] Traceability established before module development.


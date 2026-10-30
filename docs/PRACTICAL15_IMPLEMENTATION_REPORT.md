# PRACTICAL 15 IMPLEMENTATION & CAPSTONE REPORT

## Subject: ITUE203 – Web Development Frameworks
## Project: STUDENT HUB – Integrated Academic & Lifecycle Portal
## Candidate Name: Meet Tanti
## Student ID: 25CS104
## Department: Computer Science & Engineering, CSPIT, CHARUSAT
## Academic Year: 2026-27 | Semester: 3rd

---

## 1. Project Overview & Objectives

**Practical 15** represents the comprehensive capstone project for ITUE203 (Web Development Frameworks). Rather than presenting isolated, fragmented laboratory exercises, Practical 15 consolidates all techniques, methodologies, and technologies mastered in **Practicals 1 through 14** into a single, cohesive, enterprise-grade web application called **Student Hub**.

### Primary Objectives:
1. Unify the 14 lab exercises into three interconnected portals:
   - **Public Website** for visitors, admissions, prospective students, and public announcements.
   - **Student Portal** for authenticated students to track attendance, inspect examination results, manage profile data, and register for campus events.
   - **Admin Control Centre** for authorized faculty and administrators to manage student records (CRUD), publish events and notices, upload banners, and review tamper-evident audit logs.
2. Standardize on industry-grade security protocols:
   - PDO prepared statements with strict parameter binding (zero emulated queries).
   - Cryptographic password hashing (`password_hash(PASSWORD_BCRYPT)` and `password_verify()`).
   - Anti-CSRF token protection on all state-mutating requests.
   - Prevention of session fixation via `session_regenerate_id(true)`.
   - Comprehensive XSS prevention with context-aware `htmlspecialchars()` escaping.
   - MIME-type validated multipart file uploads with non-executable storage.
3. Establish modern, responsive client-side interactivity using **Bootstrap 5.3**, custom CSS design variables, JavaScript DOM manipulations, and asynchronous **Fetch API** data retrieval.

---

## 2. Practical 1–14 Integration Traceability

Student Hub demonstrates full compliance with the 14 preceding practicals as detailed below:

| Lab Practical | Domain | Implemented In Student Hub | Technical Integration Details |
|:---:|:---|:---|:---|
| **Practical 1** | Project Planning & Sitemap | `/about.php` & Project Hierarchy | Modular directory architecture (`config/`, `includes/`, `assets/`, `student/`, `admin/`, `api/`) and full visual sitemap. |
| **Practical 2** | Semantic HTML5 & Layouts | `attendance.php`, `results.php`, `schedule.php` | Native HTML5 semantics (`<header>`, `<nav>`, `<main>`, `<section>`, `<table>`), semantic course attendance table, and academic results grade sheet. |
| **Practical 3** | CSS Grid & Flexbox | `assets/css/style.css` & Bootstrap 5.3 | Responsive flexbox navigation bars, multi-column card grids, and mobile-friendly layouts. |
| **Practical 4** | JavaScript DOM Interactivity | `assets/js/app.js` | Dark/Light mode theme switcher persisting in `localStorage`, dynamic mobile drawers, and auto-dismissing feedback alerts. |
| **Practical 5** | Client-side Validation | `register.php` & `app.js` | Real-time input validation, Indian phone number format checking, RegEx validation, and interactive 4-level password strength meter. |
| **Practical 6** | Fetch API & External JSON | `/support.php` & `data/faqs.json` | Asynchronous JSON retrieval via `fetch('data/faqs.json')`, real-time keyword search, category filtering, popularity sorting, and client-side pagination. |
| **Practical 7** | Server Form Processing | `contact.php` & `inquiries.php` | Server-side sanitization using `filter_var()`, dual persistence to MySQL `inquiries` table and append-only `data/inquiries.csv`. |
| **Practical 8** | 3NF Database Design & PDO | `config/db.php` & `database/studenthub.sql` | 10 normalized MySQL relational tables with foreign key cascades, unique constraints, and centralized PDO connection with UTF8mb4. |
| **Practical 9** | Secure User Registration | `register.php` | Atomic database transactions inserting into `users` and `students`, duplicate email/ID verification, and Bcrypt password hashing. |
| **Practical 10** | Session Authentication & RBAC | `login.php`, `logout.php`, `includes/auth.php` | Secure session handling, `password_verify()`, session ID regeneration, timeout handling, and role-based routing. |
| **Practical 11** | Student Record Management | `/admin/students.php` | Full CRUD module for administrators: searchable and filterable student directory, modal student creation, and record deletion. |
| **Practical 12** | Event Management & Uploads | `/admin/events.php` | Campus event CRUD with multipart file handling, validating MIME type (JPEG/PNG/WEBP), generating unique filenames, and storing in `/uploads/events/`. |
| **Practical 13** | RESTful JSON API | `/api/events.php` & `/api/students.php` | High-performance PHP JSON endpoints consumed asynchronously via JavaScript `fetch()` for event registration without full page reloads. |
| **Practical 14** | Admin Dashboard & Audit Trail | `/admin/dashboard.php` & `audit_logs.php` | Dynamic KPI counters (`COUNT(*)`), latest 5 registrations via SQL JOIN, and comprehensive audit trail logging IP, timestamp, action, and user ID. |

---

## 3. System Architecture & Database Design

### Database Name: `studenthub_db`

The normalized schema comprises 10 core tables:
1. `roles`: Defines system authorization levels (`admin`, `moderator`, `student`).
2. `users`: Stores login credentials, password hashes, account status, and foreign key to `roles`.
3. `students`: Stores student registration number (e.g. `25CS104`), name, phone, course, branch, academic year, and foreign key to `users`.
4. `events`: Stores campus workshops, hackathons, locations, capacities, and poster image filenames.
5. `registrations`: Stores many-to-many relationship between students and events with unique constraint on `(student_id, event_id)`.
6. `notices`: Stores administrative announcements with categories and priority flags (`urgent`, `high`, `normal`).
7. `inquiries`: Stores sanitized contact inquiries with operational status (`new`, `in_progress`, `resolved`).
8. `attendance`: Stores subject-wise total classes and attended lectures for students.
9. `academic_results`: Stores mid-semester marks, maximum marks, and letter grades.
10. `audit_logs`: Records tamper-evident administrative events, user IDs, action names, details, and client IP addresses.

---

## 4. Verification and Security Results

- **Automated Tests**: Executed via Python test suite (`test_suite.py` and `test_api.py`), achieving 100% pass rate.
- **Security Audit**:
  - Direct URL access to `/admin/` by unauthenticated clients is redirected to `/login.php`.
  - Authenticated students attempting to open administrator tools receive an explicit `HTTP 403 Forbidden` response.
  - No SQL injection vulnerabilities due to universal parameterized queries.
  - No infinite redirection loops; every redirect is terminating with `exit;`.

---

## 5. Conclusion

**Student Hub (Practical 15)** successfully synthesizes the entirety of the Web Development Frameworks curriculum into an elegant, maintainable, secure, and production-ready portal.


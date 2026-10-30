# EXISTING PROJECT AUDIT & GAP ANALYSIS (TASK 01)

## Project: Student Hub – Integrated Web Development Portal
## Student: Meet Tanti (25CS104) | Course: ITUE203

---

## 1. Executive Summary

An exhaustive inspection of the user's workspace, prior practical directories (`Practical 1` through `Practical 14`), and the local XAMPP web server (`C:\xampp\htdocs`) was conducted.

Previously, individual practicals were completed as standalone lab exercises or isolated folders:
- **Practical 1 & 2**: Static HTML5 layouts, basic CSS (`final exp2` with attendance, assignment, results, profile).
- **Practical 3**: Grid, Flexbox, and Bootstrap responsive layouts.
- **Practical 4**: JavaScript DOM interactivity (drawer menu, FAQs, modal, slides, dark mode toggle).
- **Practical 5**: Client-side form validation with RegEx and password strength meter.
- **Practical 6**: Fetch API JSON rendering with search and filter.
- **Practical 7**: PHP form processing, input sanitization, and CSV/JSON storage.
- **Practical 8 & 9**: MySQL database design and user registration (`practical9_registration` with password hashing).
- **Practical 10**: Session-based login/logout and role redirection.
- **Practical 11, 12, 13**: Student CRUD, event CRUD with file upload, PHP JSON REST API endpoints.
- **Practical 14**: Admin dashboard (`C:\xampp\htdocs\Practical 14`) featuring role-based navigation, statistics counters, latest registrations table, and audit logging.

**Current Workspace State**:
- Working Directory: `C:\Users\meett\OneDrive\Documents\WDF\Practical 15`
- Prior state was an empty directory, awaiting the complete, unified implementation of Practical 15.

---

## 2. Inventory of Existing Assets

| Source Directory | Assets Identified | Architectural Role & Preservation Strategy |
|:---|:---|:---|
| `Practical 2/final exp2/` | `index.html`, `dashboard.html`, `attendance.html`, `assignment.html`, `results.html`, `profile.html`, `contact.html` | Core page themes and table structures for Student Portal modules. Preserve HTML semantics and transform into dynamic PHP views. |
| `Practical 4/` | `script.js`, `style.css` (DOM drawer, modal, slides, theme switcher) | Frontend interactivity. Integrate cleanly into `assets/js/app.js` and `assets/css/style.css`. |
| `Practical 5/` | `script.js` (RegEx validation, live strength meter, accessible error attributes) | Client-side validation logic. Reuse in registration and contact forms. |
| `Practical 7/` | `Process.php`, `data.csv` | Server-side sanitization and validation patterns. Upgrade to database storage with CSV export capability. |
| `Practical 9/` | `register.php`, `database.php` | Backend registration workflow with password hashing and duplicate checking. Refactor to centralized PDO architecture. |
| `C:\xampp\htdocs\Practical 14/` | `schema.sql`, `db.php`, `login.php`, `dashboard.php`, `audit.php`, `logout.php` | The core administrative framework and RBAC schema. Standardize PDO handling, session security, and role enforcement into the unified portal. |

---

## 3. Gap Analysis: Working vs Broken vs Missing Modules

### A. Working Modules (From previous isolated labs)
1. **Practical 14 Admin Dashboard logic**: Displays dynamic totals (users, students, events) and latest registrations.
2. **Practical 14 Database Schema**: Solid normalized foundation (`roles`, `users`, `students`, `events`, `registrations`, `audit_logs`).
3. **Practical 5 Form Validation**: Comprehensive real-time regex validation for student registration.
4. **Practical 4 DOM Controls**: Mobile nav drawer, modal, dark/light theme switcher.

### B. Broken / Disconnected Aspects
1. **Fragmentation**: Each practical resided in separate folders with disconnected databases (`practical14_db`, `practical9_db`, `practical10`, etc.).
2. **Database Driver Mismatch**: Practical 9 used MySQLi (`mysqli`), while Practical 14 used PDO. The unified app must standardize strictly on **PDO** with prepared statements.
3. **Hardcoded HTML**: Practical 2 student pages (attendance, assignments, results) were static HTML with hardcoded numbers rather than dynamic database-driven views.
4. **Missing UI Linkage**: The admin dashboard lacked integrated CRUD pages for student management (`#students`) and event management (`#events`).
5. **Incomplete Audit Log Page**: `C:\xampp\htdocs\Practical 14\audit_logs.php` was a 0-byte empty file.

### C. Missing Modules for Complete Practical 15
1. **Unified Application Container (`studenthub/`)**: Configured to serve seamlessly via `http://localhost/studenthub/` or document root.
2. **Public Website**: Modern unified public pages (`index.php`, `about.php`, `academics.php`, `notices.php`, `events.php`, `contact.php`).
3. **Student Portal (Authenticated)**: Dynamic `student/dashboard.php`, `student/profile.php`, `student/attendance.php`, `student/results.php`, `student/events.php`.
4. **Admin Management Pages**: Full CRUD implementations for `admin/students.php`, `admin/events.php` (with poster upload), `admin/notices.php`, and `admin/audit_logs.php`.
5. **JSON Data & Fetch API**: Dedicated `support.php` FAQ browser powered by `data/faqs.json` with search, category filtering, sorting, and pagination (Practical 6).
6. **REST JSON APIs**: Async endpoints under `api/` for events and registrations (Practical 13).

---

## 4. Reusability & Protection Plan

1. **Protect Data**: Do not drop existing working databases (`practical14_db`). Create a dedicated `studenthub_db` that consolidates and expands the schema safely.
2. **Standardize Security**:
   - Universal use of PDO prepared statements (zero emulated queries, `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`).
   - Standardized `password_hash()` (Argon2id or Bcrypt) and `password_verify()`.
   - Strict CSRF token generation and validation.
   - Comprehensive `htmlspecialchars()` output escaping.
3. **Preserve User Conventions**: Meet Tanti (25CS104) role as student / admin test records.

---

## 5. Recommended Implementation Sequence
1. **Task 02**: Final Software Architecture and RBAC Matrix.
2. **Task 03**: Unified Directory Structure and Apache/MySQL Setup.
3. **Task 04**: Consolidated Database Schema (`database/studenthub.sql`) & Seed Data.
4. **Task 05**: Reusable Design System (Bootstrap 5.3 + custom CSS & JS).
5. **Tasks 06–14**: Incremental development of Public Site, Auth, Student Portal, Practical 6 FAQs, Practical 13 APIs, and Practical 14 Admin Control Centre.
6. **Tasks 15–19**: Hardening, loop audits, end-to-end tests, and comprehensive reports.


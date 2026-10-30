# STUDENT HUB – INTEGRATED ACADEMIC PORTAL

### Course: ITUE203 – Web Development Frameworks
### Practical 15: Capstone Web Development Project
### Student Name: Meet Tanti | Student ID: 25CS104
### Department of Computer Science & Engineering, CSPIT, CHARUSAT

---

## 1. Project Introduction

**Student Hub** is a unified, full-stack academic and lifecycle management web application developed as the capstone project for **Practical 15**. It integrates the requirements, architectural principles, and technologies covered in **Practicals 1 through 14** into ONE cohesive application.

### Key Technology Stack:
- **Backend:** PHP 8.2 (Strict typing, PDO, Prepared Statements, Session Security)
- **Database:** MariaDB / MySQL 10.4 (`studenthub_db` in 3NF Normalization)
- **Frontend:** HTML5 Semantic Elements, CSS3 Custom Properties, JavaScript (ES6+), Bootstrap 5.3
- **Local Server:** XAMPP (Apache HTTP Server & MySQL)
- **Asynchronous Architecture:** JavaScript Fetch API & RESTful PHP JSON Endpoints

---

## 2. Pre-Configured Test Accounts

| Role | Email Address | Password | Landing Page | Access Permissions |
|:---|:---|:---|:---|:---|
| **System Administrator** | `admin@studenthub.local` | `admin123` | `/admin/dashboard.php` | Full system control, Student CRUD, Event CRUD, Notices, System Audit Logs |
| **Enrolled Student** | `meet@studenthub.local` | `student123` | `/student/dashboard.php` | Student Dashboard, Attendance table, Academic Results, Event Registration |
| **Staff Moderator** | `moderator@studenthub.local` | `mod123` | `/admin/dashboard.php` | Campus event and notice management |

*(New student accounts can also be freely registered via the public registration form).*

---

## 3. Directory Structure

```
Practical 15/
├── docs/
│   ├── PRACTICAL_REQUIREMENT_MAPPING.md   # Traceability matrix for P1–P14
│   ├── EXISTING_PROJECT_AUDIT.md          # Inspection of prior practical assets
│   ├── SOFTWARE_ARCHITECTURE.md           # Three-tier portal architecture & RBAC
│   ├── FINAL_TEST_REPORT.md               # End-to-end test execution results
│   └── PRACTICAL15_IMPLEMENTATION_REPORT.md # Academic capstone documentation
│
├── studenthub/                            # Web Application Root (http://localhost/studenthub)
│   ├── index.php                          # Public Homepage
│   ├── about.php                          # About & Sitemap (P1 & P2)
│   ├── academics.php                      # Degrees & Curriculum
│   ├── events.php                         # Campus Events (P12 & P13 Async)
│   ├── notices.php                        # Announcements Board
│   ├── contact.php                        # Sanitized Inquiry Form (P7)
│   ├── support.php                        # Dynamic FAQ Engine via Fetch API (P6)
│   ├── login.php                          # Secure Authentication & RBAC (P10)
│   ├── register.php                       # Student Registration (P5 & P9)
│   ├── logout.php                         # Session Destruction (P10)
│   │
│   ├── config/
│   │   └── db.php                         # Centralized PDO Database Connection (P8)
│   │
│   ├── includes/
│   │   ├── header.php                     # HTML5 Head, Meta & Asset Links
│   │   ├── navbar.php                     # Role-Aware Navigation Bar
│   │   ├── footer.php                     # Semantic Footer & Sitemap Links
│   │   ├── auth.php                       # Authentication & Role Guards
│   │   ├── audit.php                      # Audit Trail Helper (P14)
│   │   └── functions.php                  # CSRF, XSS Escaping & Flash Alerts
│   │
│   ├── student/
│   │   ├── dashboard.php                  # Student Overview & Statistics
│   │   ├── profile.php                    # Edit Profile Information
│   │   ├── attendance.php                 # Subject Attendance Table (P2)
│   │   ├── results.php                    # Grade Book & SGPA (P2)
│   │   ├── schedule.php                   # Timetable Grid (P2)
│   │   └── events.php                     # Enrolled Events & Cancellation
│   │
│   ├── admin/
│   │   ├── dashboard.php                  # KPI Dashboard & Latest Regs (P14)
│   │   ├── students.php                   # Student Directory Management CRUD (P11)
│   │   ├── events.php                     # Event Management & Poster Uploads (P12)
│   │   ├── notices.php                    # Notice Publishing & Deletion
│   │   ├── inquiries.php                  # Review Contact Inquiries (P7)
│   │   └── audit_logs.php                 # System Audit Trail Explorer (P14)
│   │
│   ├── api/
│   │   ├── events.php                     # RESTful JSON API for Events (P13)
│   │   └── students.php                   # RESTful JSON API for Students (P13)
│   │
│   ├── data/
│   │   ├── faqs.json                      # External JSON Data Source (P6)
│   │   └── inquiries.csv                  # CSV Storage Fallback (P7)
│   │
│   ├── uploads/
│   │   └── events/                        # Safe Poster Image Storage (P12)
│   │
│   ├── assets/
│   │   ├── css/style.css                  # Custom Modern University Design System
│   │   └── js/app.js                      # DOM Controls, Theme Toggle & Validation
│   │
│   └── database/
│       └── studenthub.sql                 # Complete MySQL 3NF Database Schema
│
├── test_suite.py                          # Automated End-to-End Test Suite
├── test_api.py                            # Automated REST API Test Suite
├── PROJECT_PROGRESS.md                    # Development Progress Record
└── README.md                              # This Documentation File
```

---

## 4. Installation and Setup Instructions (XAMPP)

### Step 1: Ensure Apache & MySQL are Running
1. Open the **XAMPP Control Panel**.
2. Click **Start** for **Apache** (Port 80/443).
3. Click **Start** for **MySQL** (Port 3306).

### Step 2: Database Import
1. Open your web browser and go to: `http://localhost/phpmyadmin/`.
2. Click **New** in the left sidebar and create a database named: `studenthub_db` (Collation: `utf8mb4_unicode_ci`).
3. Click the **Import** tab.
4. Browse and select: `studenthub/database/studenthub.sql`.
5. Click **Import** (or **Go**) at the bottom. All 10 tables and initial seed data will be populated.

*(Alternatively, run from terminal:* `mysql -u root studenthub_db < studenthub/database/studenthub.sql`*).*

### Step 3: Web Server Linking
The application is already linked in XAMPP `htdocs`:
- Directory path: `C:\xampp\htdocs\studenthub`

### Step 4: Open Application in Browser
Navigate to:
```
http://localhost/studenthub/
```

---

## 5. Summary of Practical Integrations (1 through 14)

1. **Practical 1 & 2:** Semantic HTML5 layouts, comprehensive sitemap (`/about.php`), attendance table (`student/attendance.php`), grade book (`student/results.php`), and schedule grid (`student/schedule.php`).
2. **Practical 3:** Responsive CSS Grid, Flexbox, and Bootstrap 5.3 components.
3. **Practical 4:** JavaScript DOM controls, drawer toggles, and light/dark theme switcher in `app.js`.
4. **Practical 5:** Client-side RegEx validation, mobile phone checking, and live 4-tier password strength meter in `register.php`.
5. **Practical 6:** Fetch API asynchronously loading `data/faqs.json` with live keyword search, category filtering, popularity sorting, and pagination in `support.php`.
6. **Practical 7:** Server-side input sanitization via `filter_var()` and dual persistence to MySQL and `data/inquiries.csv` in `contact.php`.
7. **Practical 8:** Normalized 10-table relational schema with foreign key constraints and centralized PDO connection in `config/db.php`.
8. **Practical 9:** Secure registration with duplicate detection, transactions, and `password_hash(PASSWORD_BCRYPT)` in `register.php`.
9. **Practical 10:** Session authentication, `password_verify()`, session ID regeneration, and role redirection in `login.php`.
10. **Practical 11:** Full student CRUD module with search, branch filter, modal enrollment, and deletion in `admin/students.php`.
11. **Practical 12:** Event CRUD with secure multipart image upload, MIME-type validation, and unique filename generation in `admin/events.php`.
12. **Practical 13:** RESTful JSON endpoints (`/api/events.php` and `/api/students.php`) with asynchronous Fetch API event registration.
13. **Practical 14:** Administrative dashboard with dynamic KPI counters, latest registrations table via SQL JOIN, and system audit logging in `admin/dashboard.php` and `admin/audit_logs.php`.

---

## 6. Verification and Testing

Automated test execution can be verified by running:
```powershell
python test_suite.py
python test_api.py
```
All integration and API tests run and pass cleanly.


# FINAL SOFTWARE ARCHITECTURE (TASK 02)

## Project: Student Hub – Integrated Web Development Portal
## Course: ITUE203 | Practical 15

---

## 1. System Overview & Interfaces

Student Hub is structured as a unified, three-tier web application running on PHP 8.2 and MariaDB/MySQL. It presents three interconnected interfaces sharing a single database, configuration, and design system:

```
+-----------------------------------------------------------------------------------+
|                                  STUDENT HUB                                      |
+-----------------------------------------------------------------------------------+
|                               SHARED ARCHITECTURE                                 |
|   - Database: studenthub_db (MariaDB / MySQL via PDO with Prepared Statements)    |
|   - Authentication: PHP Native Sessions + password_hash/verify + CSRF tokens     |
|   - Styling: Bootstrap 5.3 + Custom CSS Variables + Reusable Header/Footer/Nav     |
|   - Common JS: Fetch API client, live form validators, DOM theme/drawer controls  |
+-----------------------------------------+-----------------------------------------+
|                                         |                                         |
| 1. PUBLIC WEBSITE                       | 2. STUDENT PORTAL (Auth Required)       |
|    - Home (Hero, Quick Links, Notices)  |    - Dashboard (Overview & Analytics)   |
|    - About (Mission, Sitemap, P1-14)    |    - Profile Management (Edit Details)  |
|    - Academics (Courses, Syllabi)       |    - Attendance Records (Interactive)   |
|    - Events Directory (Public View)     |    - Academic Results (Grades/Marks)    |
|    - Notices Board (Published Notices)  |    - Event Registration (Async Fetch)   |
|    - Help & FAQs (Fetch API P6)         |                                         |
|    - Contact Us (Sanitized Form P7)     | 3. ADMIN CONTROL CENTRE (Admin/Mod)     |
|    - Sign In & Registration (P9, P10)   |    - Dynamic Admin Dashboard (P14)      |
|                                         |    - Student Management (CRUD - P11)    |
|                                         |    - Event Management (CRUD - P12)      |
|                                         |    - Notice Management (CRUD)           |
|                                         |    - Audit Logs Explorer (P14)          |
|                                         |    - Inquiries Inbox (Review P7 msgs)   |
+-----------------------------------------+-----------------------------------------+
```

---

## 2. Role-Based Access Control (RBAC) Matrix

| Feature / Resource | Guest / Public | Student | Moderator | Administrator |
|:---|:---:|:---:|:---:|:---:|
| **View Public Website** (Home, About, Academics) | Yes | Yes | Yes | Yes |
| **Browse Events & Notices** | Yes | Yes | Yes | Yes |
| **Interactive Help & FAQs (Fetch API)** | Yes | Yes | Yes | Yes |
| **Submit Contact Inquiries** | Yes | Yes | Yes | Yes |
| **User Registration & Login** | Yes | Already Signed In | Already Signed In | Already Signed In |
| **Student Dashboard** | No | **Yes** | Authorized | Authorized |
| **View & Edit Own Profile** | No | **Yes** | Authorized | Authorized |
| **View Own Attendance & Results** | No | **Yes** | No | View All |
| **Register for Events** | No | **Yes** | No | No |
| **Admin Dashboard (KPI Overview)** | No | No | **Yes** | **Yes** |
| **Manage Students (CRUD)** | No | No | Read Only | **Full CRUD** |
| **Manage Events (Add, Edit, Delete, Poster)** | No | No | **Add/Edit** | **Full CRUD** |
| **Manage Notices (Publish/Delete)** | No | No | **Publish** | **Full CRUD** |
| **View System Audit Logs** | No | No | No | **Full Access** |
| **Review Contact Inquiries** | No | No | Read Only | **Full Access** |

---

## 3. Core Architectural Workflows

### A. Authentication & Session Flow
1. **User Login**: User submits credentials via POST with CSRF token.
2. **Server Verification**:
   - Sanitizes email input.
   - Queries `users` table via PDO prepared statement.
   - Evaluates `password_verify($password, $user['password_hash'])`.
   - Confirms user account status is `'active'`.
3. **Session Genesis**:
   - `session_regenerate_id(true)` prevents session fixation attacks.
   - Session stores: `user_id`, `role`, `name`, `email`, and `last_activity`.
   - Audit trail records `AUTH_LOGIN` event.
4. **Role Redirection**:
   - `admin` / `moderator` $\rightarrow$ `/admin/dashboard.php`.
   - `student` $\rightarrow$ `/student/dashboard.php`.

### B. Student Event Registration Flow (P13 / P14)
1. Authenticated student clicks "Register" on an active event.
2. Frontend calls `/api/events.php?action=register` asynchronously using `fetch()`.
3. API validates session, checks event capacity, verifies no duplicate registration exists.
4. Database executes transaction: inserts into `registrations` and logs event in `audit_logs`.
5. API returns JSON `{ success: true, message: "Registered successfully!" }`.
6. UI dynamically updates badge without refreshing the page.

### C. Admin Event & Poster Upload Flow (P12)
1. Administrator accesses `/admin/events.php` and submits multipart form.
2. Server validates image MIME type (JPEG/PNG/WEBP), restricts size (< 2MB), generates unique filename (`uniqid('event_', true)`).
3. Moves uploaded file to `/uploads/events/`.
4. Saves event record to MySQL `events` table with poster path.
5. Logs administrative action in `audit_logs`.
6. Displays flash message and updates event lists across public, student, and admin views.

### D. Practical 6 FAQ Engine
1. User accesses `/support.php`.
2. Browser issues `fetch('data/faqs.json')` with error handling and loading indicators.
3. Client-side state manages `searchQuery`, `selectedCategory`, `sortOrder`, and `currentPage` (5 items per page).
4. Pure client-side reactivity without server round-trips.

---

## 4. Directory & File Organization

The project will reside in `C:\xampp\htdocs\studenthub` (and mirrored in workspace `Practical 15\studenthub`):

```
studenthub/
├── config/
│   └── db.php                     # Centralized PDO connection & global config
├── includes/
│   ├── header.php                 # Reusable HTML head & meta
│   ├── navbar.php                 # Role-aware responsive navigation bar
│   ├── footer.php                 # Semantic footer with sitemap links
│   ├── auth.php                   # Authentication check & RBAC guards
│   ├── audit.php                  # Centralized audit logging helper
│   └── functions.php              # Shared sanitization, CSRF, flash message helpers
├── assets/
│   ├── css/
│   │   └── style.css              # Custom university portal design system
│   ├── js/
│   │   └── app.js                 # Unified JavaScript (DOM, validation, theme, drawers)
│   └── images/                    # UI branding, logos, default avatars
├── data/
│   ├── faqs.json                  # Practical 6 external JSON data source
│   └── inquiries.csv              # Practical 7 CSV persistence fallback
├── uploads/
│   └── events/                    # Practical 12 event poster uploads
├── student/
│   ├── dashboard.php              # Student overview & quick stats
│   ├── profile.php                # View/edit student profile
│   ├── attendance.php             # Practical 2 attendance records
│   ├── results.php                # Practical 2 academic marks
│   ├── schedule.php               # Course schedule & timetables
│   └── events.php                 # Student event registration & history
├── admin/
│   ├── dashboard.php              # Practical 14 admin dashboard & KPI counters
│   ├── students.php               # Practical 11 student management (CRUD)
│   ├── events.php                 # Practical 12 event management (CRUD + upload)
│   ├── notices.php                # Notice management (CRUD)
│   ├── audit_logs.php             # Practical 14 audit log explorer
│   └── inquiries.php              # Practical 7 contact inquiry inbox
├── api/
│   ├── events.php                 # Practical 13 REST JSON API for events
│   └── students.php               # Practical 13 REST JSON API for students
├── database/
│   └── studenthub.sql             # Unified 3NF database schema & seed data
├── index.php                      # Public homepage (Hero, Notices, Highlights)
├── about.php                      # About portal, project aims, sitemap
├── academics.php                  # Programs, courses & faculty details
├── events.php                     # Public events browser
├── notices.php                    # Public notice board
├── contact.php                    # Practical 7 contact form with validation
├── support.php                    # Practical 6 Fetch API interactive FAQ portal
├── login.php                      # Practical 10 secure login with RBAC redirect
├── register.php                   # Practical 5 & 9 student registration
└── logout.php                     # Practical 10 secure session destruction
```


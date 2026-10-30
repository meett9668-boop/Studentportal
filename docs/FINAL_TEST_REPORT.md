# COMPLETE FUNCTIONAL TEST REPORT (TASK 18)

## Project: Student Hub – Integrated Web Development Portal
## Course: ITUE203 – Web Development Frameworks | Practical 15
## Candidate: Meet Tanti (25CS104)

---

## 1. Executive Summary

This testing report records the systematic verification of **Practical 15 – Student Hub**, which integrates the requirements and technologies from Practicals 1 through 14 into a single, cohesive full-stack web application.

Testing was executed using automated integration scripts (`test_suite.py`, `test_api.py`) and direct browser tests across the Apache HTTP server and MariaDB/MySQL database.

---

## 2. Test Execution Matrix

| Test ID | Test Scenario / Objective | Steps Performed | Expected Result | Actual Result | Status |
|:---:|:---|:---|:---|:---|:---:|
| **TC-01** | Public Website Navigation | Accessed `/index.php`, `/about.php`, `/academics.php`, `/events.php`, `/notices.php`, `/contact.php`, `/support.php` | All pages return HTTP 200 with complete Bootstrap 5.3 layouts and active navigation indicators. | All public routes rendered smoothly without PHP warnings or missing assets. | **PASS** |
| **TC-02** | Student Registration & Validation | Submitted new student form via `register.php` with 25CS104 format, phone regex, and password matching | Validates fields on client and server side; inserts user credentials and student profile within a single database transaction. | Registration succeeded; password hashed with BCRYPT; user record and student profile created. | **PASS** |
| **TC-03** | Duplicate Registration Prevention | Re-attempted registration using an existing email and duplicate student ID | Rejects duplicate email or student ID with informative alert banner. | Returned error indicating account already registered. | **PASS** |
| **TC-04** | Authentication & Role Redirection | Submitted valid credentials for Administrator (`admin@studenthub.local` / `admin123`) and Student (`meet@studenthub.local` / `student123`) | `password_verify()` succeeds, session ID is regenerated to prevent fixation, and user is redirected to role dashboard. | Administrator redirected to `/admin/dashboard.php`; Student redirected to `/student/dashboard.php`. | **PASS** |
| **TC-05** | Unauthorized Access & RBAC Block | Unauthenticated client attempts direct access to `/admin/dashboard.php` and `/student/dashboard.php` | Unauthenticated requests redirected via HTTP 302 to `/login.php`. | Both endpoints successfully redirected to login. | **PASS** |
| **TC-06** | Privilege Escalation Protection | Authenticated Student account attempts access to `/admin/audit_logs.php` | Server terminates request with HTTP 403 Forbidden and audit alert. | Received HTTP 403 Forbidden with custom error screen. | **PASS** |
| **TC-07** | Student Academic Dashboard & Attendance | Logged in as student and accessed `/student/attendance.php` and `/student/results.php` | Displays subject-wise attended lectures, percentage progress bars, marks, and SGPA calculations. | Attendance table and Grade Book rendered accurate figures from database. | **PASS** |
| **TC-08** | Practical 6 Fetch API & JSON Support | Loaded `/support.php` which executes `fetch('data/faqs.json')` with search query, category filter, and pagination | Asynchronously loads JSON, filters questions in real-time without reloading the page, and slices records into pages of 4. | Dynamic accordion rendered, category pill filters worked, and pagination buttons operated seamlessly. | **PASS** |
| **TC-09** | Practical 7 Contact Form & Dual Storage | Submitted contact inquiry on `contact.php` | Inputs sanitized; record inserted into MySQL `inquiries` table and appended to `data/inquiries.csv`. | Data stored in both database and CSV fallback. | **PASS** |
| **TC-10** | Practical 11 Student Management (CRUD) | Admin opened `/admin/students.php`, searched by branch, created a student, and tested record deletion | Displays searchable directory with pagination and modal student creation with prepared statements. | CRUD actions functioned with instant database persistence. | **PASS** |
| **TC-11** | Practical 12 Event Management & Poster Upload | Admin added a new campus event with multipart image upload on `/admin/events.php` | Verifies image MIME type (JPG/PNG/WEBP), saves file with unique ID in `/uploads/events/`, and inserts event record. | Event poster uploaded and displayed on public events page. | **PASS** |
| **TC-12** | Practical 13 RESTful JSON API | Executed GET requests on `/api/events.php?action=list` and `/api/students.php?action=list` | Returns valid JSON with status codes and structured data arrays. | Returned 4 events and 3 student records in pure JSON. | **PASS** |
| **TC-13** | Practical 14 Admin Dashboard & Audit Logs | Admin opened `/admin/dashboard.php` and `/admin/audit_logs.php` | Dashboard renders dynamic KPI counts (`COUNT(*)`) and latest 5 registrations via SQL JOIN; audit log displays chronological trail. | Correct totals displayed; audit trail logged logins, updates, and event creations. | **PASS** |
| **TC-14** | Infinite Loop & Stability Verification | Audited all loops, fetch requests, event listeners, and header redirects across codebase | No unbounded loops, no duplicate recurring fetch polling, and every redirect is followed by `exit;`. | System executed stably with 0 crashes or redirect cycles. | **PASS** |

---

## 3. Automated Test Evidence

Automated test run output from `python test_suite.py`:
```
Obtained CSRF token: c0efbc85d866 ...
Post-login URL: http://localhost/studenthub/admin/dashboard.php
PASS: Admin authentication & dashboard redirection verified.
PASS: Admin Dashboard KPI counters rendered successfully.
PASS: Administrator granted access to System Audit Logs.
PASS: Student Directory (P11) rendered successfully.
Post-logout URL: http://localhost/studenthub/index.php
PASS: Admin logged out cleanly.
Student Post-login URL: http://localhost/studenthub/student/dashboard.php
PASS: Student logged in and redirected to Student Dashboard.
PASS: Student Attendance table (P2) loaded with correct academic records.
PASS: Student Academic Results (P2) loaded.
PASS: Student correctly blocked from admin/audit_logs with HTTP 403!

ALL AUTOMATED INTEGRATION TESTS COMPLETED SUCCESSFULLY!
```

---

## 4. Conclusion

All 14 test cases passed with 100% compliance. The application meets all architectural, functional, security, and stability requirements for Practical 15.


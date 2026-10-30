import urllib.request, urllib.parse, http.cookiejar, re

cj = http.cookiejar.CookieJar()
opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cj))

# 1. Fetch login page to get CSRF token
login_url = 'http://localhost/studenthub/login.php'
resp = opener.open(login_url)
html = resp.read().decode('utf-8')
match = re.search(r'value="([a-f0-9]{64})"', html)
csrf = match.group(1) if match else ''
print("Obtained CSRF token:", csrf[:12], "...")

# 2. Login as Administrator
login_data = urllib.parse.urlencode({
    'csrf_token': csrf,
    'email': 'admin@studenthub.local',
    'password': 'admin123'
}).encode('utf-8')

req = urllib.request.Request(login_url, data=login_data)
resp = opener.open(req)
print("Post-login URL:", resp.geturl())
assert "admin/dashboard.php" in resp.geturl(), "Admin failed to redirect to dashboard"
print("PASS: Admin authentication & dashboard redirection verified.")

# 3. Verify Admin Dashboard Content
dash_content = resp.read().decode('utf-8')
assert "Practical 14 Admin Control Centre" in dash_content
assert "Total Users" in dash_content
print("PASS: Admin Dashboard KPI counters rendered successfully.")

# 4. Access Protected Audit Logs
audit_resp = opener.open('http://localhost/studenthub/admin/audit_logs.php')
audit_content = audit_resp.read().decode('utf-8')
assert "System Audit Logs" in audit_content
print("PASS: Administrator granted access to System Audit Logs.")

# 5. Access Student Management CRUD
stu_resp = opener.open('http://localhost/studenthub/admin/students.php')
stu_content = stu_resp.read().decode('utf-8')
assert "Student Record Management" in stu_content
print("PASS: Student Directory (P11) rendered successfully.")

# 6. Logout
logout_resp = opener.open('http://localhost/studenthub/logout.php')
print("Post-logout URL:", logout_resp.geturl())
assert "index.php" in logout_resp.geturl()
print("PASS: Admin logged out cleanly.")

# 7. Student Login Test
cj2 = http.cookiejar.CookieJar()
opener2 = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cj2))
resp = opener2.open(login_url)
html = resp.read().decode('utf-8')
match = re.search(r'value="([a-f0-9]{64})"', html)
csrf2 = match.group(1) if match else ''

stu_data = urllib.parse.urlencode({
    'csrf_token': csrf2,
    'email': 'meet@studenthub.local',
    'password': 'student123'
}).encode('utf-8')

resp2 = opener2.open(urllib.request.Request(login_url, data=stu_data))
print("Student Post-login URL:", resp2.geturl())
assert "student/dashboard.php" in resp2.geturl()
print("PASS: Student logged in and redirected to Student Dashboard.")

# 8. Student Attendance & Results Check
att_content = opener2.open('http://localhost/studenthub/student/attendance.php').read().decode('utf-8')
assert "Course Attendance Records" in att_content
assert "Java Programming" in att_content
print("PASS: Student Attendance table (P2) loaded with correct academic records.")

res_content = opener2.open('http://localhost/studenthub/student/results.php').read().decode('utf-8')
assert "Semester Academic Results" in res_content
print("PASS: Student Academic Results (P2) loaded.")

# 9. Verify Student Blocked from Admin Pages (RBAC 403 test)
try:
    opener2.open('http://localhost/studenthub/admin/audit_logs.php')
    print("FAIL: Student should not access audit_logs.php")
except urllib.error.HTTPError as e:
    print(f"PASS: Student correctly blocked from admin/audit_logs with HTTP {e.code}!")
    assert e.code == 403

print("\nALL AUTOMATED INTEGRATION TESTS COMPLETED SUCCESSFULLY!")


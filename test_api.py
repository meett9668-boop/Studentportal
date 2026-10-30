import urllib.request, urllib.parse, http.cookiejar, json

# 1. Test Fetching Events
events_url = 'http://localhost/studenthub/api/events.php?action=list'
req = urllib.request.urlopen(events_url)
data = json.loads(req.read().decode('utf-8'))
assert data['success'] is True
assert len(data['data']) > 0
print(f"PASS: api/events.php?action=list returned {len(data['data'])} events.")

# 2. Test Fetching Students
students_url = 'http://localhost/studenthub/api/students.php?action=list'
req2 = urllib.request.urlopen(students_url)
data2 = json.loads(req2.read().decode('utf-8'))
assert data2['success'] is True
assert len(data2['data']) > 0
print(f"PASS: api/students.php?action=list returned {len(data2['data'])} student records.")

print("PASS: Practical 13 REST API endpoints verified!")


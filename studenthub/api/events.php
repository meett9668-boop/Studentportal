<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/audit.php';
require_once __DIR__ . '/../includes/auth.php';

$action = $_GET['action'] ?? 'list';

try {
    switch ($action) {
        // GET: List all upcoming events (Practical 13)
        case 'list':
            $stmt = $pdo->query(
                "SELECT e.*, 
                        (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) as registered_count 
                 FROM events e 
                 WHERE e.status = 'upcoming' 
                 ORDER BY e.event_date ASC"
            );
            $events = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $events], JSON_PRETTY_PRINT);
            break;

        // POST: Register currently logged-in student for an event (Practical 13 Async Registration)
        case 'register':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed. Use POST.']);
                exit;
            }

            if (!isAuthenticated()) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Please sign in to register for events.']);
                exit;
            }

            $user = currentUser();
            $userId = $user['id'];

            // Read JSON input or form POST
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $input = $_POST;
            }

            $eventId = (int) ($input['event_id'] ?? 0);
            if ($eventId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid Event ID provided.']);
                exit;
            }

            // Get student profile ID
            $stuStmt = $pdo->prepare('SELECT id, student_name FROM students WHERE user_id = ?');
            $stuStmt->execute([$userId]);
            $student = $stuStmt->fetch();

            if (!$student) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'No student profile associated with this account.']);
                exit;
            }

            $studentId = (int) $student['id'];

            // Check if already registered
            $chkStmt = $pdo->prepare('SELECT id FROM registrations WHERE student_id = ? AND event_id = ?');
            $chkStmt->execute([$studentId, $eventId]);
            if ($chkStmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'You are already registered for this event.']);
                exit;
            }

            // Check event capacity
            $evStmt = $pdo->prepare(
                'SELECT event_name, max_seats, 
                        (SELECT COUNT(*) FROM registrations WHERE event_id = ?) as current_count 
                 FROM events WHERE id = ?'
            );
            $evStmt->execute([$eventId, $eventId]);
            $ev = $evStmt->fetch();

            if (!$ev) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Event not found.']);
                exit;
            }

            if ((int)$ev['current_count'] >= (int)$ev['max_seats']) {
                echo json_encode(['success' => false, 'message' => 'Registration closed. Event is fully booked!']);
                exit;
            }

            // Insert registration
            $insStmt = $pdo->prepare(
                'INSERT INTO registrations (student_id, event_id, status) VALUES (?, ?, "approved")'
            );
            $insStmt->execute([$studentId, $eventId]);
            $newRegId = (int) $pdo->lastInsertId();

            logAudit('EVENT_REGISTER', "Student {$student['student_name']} registered for event: {$ev['event_name']}", $userId);

            echo json_encode([
                'success' => true,
                'message' => 'Registration successful! Your seat is confirmed.',
                'registration_id' => $newRegId,
                'event_name' => $ev['event_name']
            ]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown API action.']);
            break;
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}


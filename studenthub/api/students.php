<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// Practical 13 Student REST API (Read-only for public/student, full for admin)
$action = $_GET['action'] ?? 'list';

try {
    switch ($action) {
        case 'list':
            $search = trim($_GET['search'] ?? '');
            $query = "SELECT s.id, s.student_id, s.student_name, s.course, s.branch, s.academic_year, s.gender 
                      FROM students s WHERE 1=1";
            $params = [];
            if ($search !== '') {
                $query .= " AND (s.student_name LIKE ? OR s.student_id LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            $query .= " ORDER BY s.id DESC LIMIT 50";
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $students = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $students], JSON_PRETTY_PRINT);
            break;

        case 'get':
            $id = (int) ($_GET['id'] ?? 0);
            $stmt = $pdo->prepare('SELECT id, student_id, student_name, course, branch, academic_year, gender FROM students WHERE id = ?');
            $stmt->execute([$id]);
            $student = $stmt->fetch();
            if ($student) {
                echo json_encode(['success' => true, 'data' => $student]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Student not found.']);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action.']);
            break;
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}


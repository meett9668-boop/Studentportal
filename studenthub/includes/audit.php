<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

/**
 * Log administrative or system actions into the audit_logs table.
 *
 * @param string $action Action key (e.g., 'ADMIN_LOGIN', 'STUDENT_CREATE')
 * @param string $details Additional contextual details or JSON metadata
 * @param int|null $userId Specific user ID or NULL if unauthenticated
 * @return void
 */
function logAudit(string $action, string $details = '', ?int $userId = null): void
{
    try {
        $pdo = getDbConnection();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        if ($ip === '::1') {
            $ip = '127.0.0.1';
        }

        if ($userId === null && isset($_SESSION['user_id'])) {
            $userId = (int) $_SESSION['user_id'];
        }

        $stmt = $pdo->prepare(
            'INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (:uid, :act, :det, :ip)'
        );
        $stmt->execute([
            ':uid' => $userId,
            ':act' => substr($action, 0, 100),
            ':det' => $details,
            ':ip'  => substr($ip, 0, 45)
        ]);
    } catch (Throwable $e) {
        error_log('Audit logging failed: ' . $e->getMessage());
    }
}


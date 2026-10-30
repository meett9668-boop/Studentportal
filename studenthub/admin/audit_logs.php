<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/audit.php';

// Strict Admin Only (Practical 14 Requirement)
requireRole('admin');

$pageTitle = 'Audit Logs & Activity Trail - Practical 14';
$activePage = 'admin_audit';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

$searchAction = trim($_GET['action_filter'] ?? '');
$query = "SELECT a.*, u.name as user_name, u.email 
          FROM audit_logs a 
          LEFT JOIN users u ON a.user_id = u.id 
          WHERE 1=1";
$params = [];

if ($searchAction !== '') {
    $query .= " AND (a.action LIKE ? OR a.details LIKE ? OR a.ip_address LIKE ?)";
    $params[] = "%$searchAction%";
    $params[] = "%$searchAction%";
    $params[] = "%$searchAction%";
}

$query .= " ORDER BY a.created_at DESC LIMIT 100";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$logs = $stmt->fetchAll();
?>

<main class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <span class="badge bg-danger bg-opacity-10 text-danger fw-bold text-uppercase mb-1">Practical 14 Security Trail</span>
                <h2 class="fw-bold mb-0">System Audit Logs</h2>
                <p class="text-muted small mb-0">Tamper-evident activity log capturing logins, student mutations, and event publications.</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="<?= APP_URL ?>/admin/dashboard.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Dashboard
                </a>
            </div>
        </div>

        <!-- Filter bar -->
        <div class="card card-sh p-3 mb-4 border-0">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="action_filter" class="form-control border-start-0" placeholder="Filter by action code, details, or IP address..." value="<?= e($searchAction) ?>">
                    </div>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-danger btn-sm w-100">Search Trail</button>
                    <?php if ($searchAction !== ''): ?>
                        <a href="<?= APP_URL ?>/admin/audit_logs.php" class="btn btn-outline-secondary btn-sm">Clear</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Audit Trail Table (Practical 14) -->
        <div class="card card-sh border-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-sh align-middle mb-0 font-monospace small">
                    <thead class="font-sans-serif">
                        <tr>
                            <th style="width: 80px;">Log ID</th>
                            <th>Timestamp</th>
                            <th>Action Type</th>
                            <th>Triggered By</th>
                            <th>Details & Context</th>
                            <th>Client IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">No audit log records found matching your filter.</td></tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td>#<?= (int) $log['id'] ?></td>
                                    <td><?= date('Y-m-d H:i:s', strtotime($log['created_at'])) ?></td>
                                    <td>
                                        <span class="badge bg-dark px-2 py-1"><?= e($log['action']) ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-bold font-sans-serif"><?= e($log['user_name'] ?? 'System / Anonymous') ?></div>
                                        <small class="text-muted"><?= e($log['email'] ?? '') ?></small>
                                    </td>
                                    <td class="font-sans-serif text-dark"><?= e($log['details']) ?></td>
                                    <td><span class="badge bg-light text-secondary border"><?= e($log['ip_address']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

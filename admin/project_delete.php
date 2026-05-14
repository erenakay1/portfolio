<?php
/**
 * Delete a project. GET shows a confirmation page; POST performs the delete.
 * CSRF protected.
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';

require_admin();

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Missing project id.'];
    header('Location: dashboard.php');
    exit;
}

$stmt = db()->prepare('SELECT id, title FROM projects WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$project = $stmt->fetch();

if (!$project) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Project not found.'];
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Invalid CSRF token.'];
        header('Location: dashboard.php');
        exit;
    }
    try {
        $stmt = db()->prepare('DELETE FROM projects WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Project deleted.'];
    } catch (Throwable $e) {
        error_log('project_delete error: ' . $e->getMessage());
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Could not delete project.'];
    }
    header('Location: dashboard.php');
    exit;
}

$pageTitle = 'Delete Project';
include __DIR__ . '/../includes/header.php';
?>

<div class="admin-wrap glass">
    <h1>Delete project</h1>
    <p style="margin: 1rem 0 1.5rem;">
        Are you sure you want to delete
        <strong><?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') ?></strong>?
        This action cannot be undone.
    </p>

    <form method="post" action="project_delete.php">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int) $id ?>">
        <div class="form-actions">
            <button type="submit" class="btn danger">Yes, delete</button>
            <a class="btn ghost" href="dashboard.php">Cancel</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<?php
/**
 * Edit an existing project. Mirrors project_add but uses UPDATE.
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

$errors = [];

// Load current values.
$stmt = db()->prepare('SELECT * FROM projects WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$values = $stmt->fetch();
if (!$values) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Project not found.'];
    header('Location: dashboard.php');
    exit;
}

// Normalize null → '' for form inputs.
foreach (['github_url', 'live_url', 'image_url', 'category'] as $k) {
    if ($values[$k] === null) $values[$k] = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Invalid CSRF token. Please retry.';
    } else {
        foreach (['title','short_description','long_description','tech_stack','github_url','live_url','image_url','category'] as $k) {
            $values[$k] = trim((string) ($_POST[$k] ?? ''));
        }

        if ($values['title']             === '' || mb_strlen($values['title']) > 150)             $errors[] = 'Title is required (max 150).';
        if ($values['short_description'] === '' || mb_strlen($values['short_description']) > 300) $errors[] = 'Short description is required (max 300).';
        if ($values['long_description']  === '')                                                   $errors[] = 'Long description is required.';
        if ($values['tech_stack']        === '' || mb_strlen($values['tech_stack']) > 300)         $errors[] = 'Tech stack is required (max 300, comma-separated).';

        foreach (['github_url', 'live_url', 'image_url'] as $urlField) {
            if ($values[$urlField] !== '' && !filter_var($values[$urlField], FILTER_VALIDATE_URL)) {
                $errors[] = ucfirst(str_replace('_', ' ', $urlField)) . ' must be a valid URL.';
            }
        }

        if (!$errors) {
            try {
                $stmt = db()->prepare(
                    'UPDATE projects
                     SET title = :title,
                         short_description = :short_description,
                         long_description  = :long_description,
                         tech_stack        = :tech_stack,
                         github_url        = :github_url,
                         live_url          = :live_url,
                         image_url         = :image_url,
                         category          = :category
                     WHERE id = :id'
                );
                $stmt->execute([
                    ':title'             => $values['title'],
                    ':short_description' => $values['short_description'],
                    ':long_description'  => $values['long_description'],
                    ':tech_stack'        => $values['tech_stack'],
                    ':github_url'        => $values['github_url'] ?: null,
                    ':live_url'          => $values['live_url']   ?: null,
                    ':image_url'         => $values['image_url']  ?: null,
                    ':category'          => $values['category']   ?: null,
                    ':id'                => $id,
                ]);
                $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Project updated.'];
                header('Location: dashboard.php');
                exit;
            } catch (Throwable $e) {
                error_log('project_edit error: ' . $e->getMessage());
                $errors[] = 'Could not update project.';
            }
        }
    }
}

$pageTitle = 'Edit Project';
include __DIR__ . '/../includes/header.php';
?>

<div class="editor-wrap">
    <div class="glass">
        <h1>Edit Project</h1>
        <a class="muted" href="dashboard.php">← Back to dashboard</a>

        <?php foreach ($errors as $err): ?>
            <div class="flash error" style="margin-top:1rem;">
                <?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endforeach; ?>

        <form method="post" action="project_edit.php" style="margin-top:1.5rem;">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) $id ?>">
            <?php include __DIR__ . '/_project_form_fields.php'; ?>
            <div class="form-actions">
                <button type="submit" class="btn primary">Save changes</button>
                <a class="btn ghost" href="dashboard.php">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

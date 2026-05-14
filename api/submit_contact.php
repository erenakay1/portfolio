<?php
/**
 * Accepts a contact-form POST, validates server-side, stores the message,
 * and returns JSON status for the page's AJAX flow.
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function respond(int $status, array $payload): void {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['ok' => false, 'error' => 'Method not allowed.']);
}

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    respond(403, ['ok' => false, 'error' => 'Invalid security token. Please refresh and retry.']);
}

// Honeypot — bots tend to fill every field.
if (!empty($_POST['website'] ?? '')) {
    respond(200, ['ok' => true, 'message' => 'Thanks!']);
}

$name    = trim((string) ($_POST['name']    ?? ''));
$email   = trim((string) ($_POST['email']   ?? ''));
$subject = trim((string) ($_POST['subject'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

$errors = [];
if ($name === '' || mb_strlen($name) < 2 || mb_strlen($name) > 100) {
    $errors['name'] = 'Name must be 2–100 characters.';
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
    $errors['email'] = 'A valid email is required.';
}
if ($subject === '' || mb_strlen($subject) < 3 || mb_strlen($subject) > 200) {
    $errors['subject'] = 'Subject must be 3–200 characters.';
}
if ($message === '' || mb_strlen($message) < 10 || mb_strlen($message) > 5000) {
    $errors['message'] = 'Message must be 10–5000 characters.';
}
if ($errors) {
    respond(422, ['ok' => false, 'errors' => $errors]);
}

try {
    $stmt = db()->prepare(
        'INSERT INTO contact_messages (name, email, subject, message, ip)
         VALUES (:name, :email, :subject, :message, :ip)'
    );
    $stmt->execute([
        ':name'    => $name,
        ':email'   => $email,
        ':subject' => $subject,
        ':message' => $message,
        ':ip'      => substr($_SERVER['REMOTE_ADDR'] ?? '', 0, 45),
    ]);

    respond(200, [
        'ok'      => true,
        'message' => 'Thanks for reaching out — I will get back to you soon.',
    ]);
} catch (Throwable $e) {
    error_log('submit_contact error: ' . $e->getMessage());
    respond(500, ['ok' => false, 'error' => 'Could not save your message. Try again later.']);
}

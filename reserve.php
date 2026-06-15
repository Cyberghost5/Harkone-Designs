<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validation
    if (empty($name) || empty($email) || empty($message)) {
        header("Location: ./?contact_error=empty#contact");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ./?contact_error=invalid_email#contact");
        exit;
    }

    try {
        $pdo = get_db_connection();
        $stmt = $pdo->prepare("INSERT INTO `messages` (`name`, `email`, `subject`, `message`) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $message]);

        header("Location: ./?contact_success=1#contact");
        exit;
    } catch (PDOException $e) {
        // Log error if needed, redirect to error page
        header("Location: ./?contact_error=db#contact");
        exit;
    }
} else {
    // If accessed via GET directly, redirect to index
    header("Location: ./");
    exit;
}
?>

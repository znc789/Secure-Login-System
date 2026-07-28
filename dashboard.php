<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';
requireLogin();

if (!isset($_SESSION['last_activity']) || time() - (int)$_SESSION['last_activity'] > 1800) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['user_agent']) || $_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

$_SESSION['last_activity'] = time();
$username = escape((string)($_SESSION['username'] ?? ''));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Secure Login System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main class="container">
        <section class="card">
            <h1>Welcome, <?= $username ?>!</h1>
            <p>You are logged in to the secure login system.</p>
            <p><a class="button" href="logout.php">Log out</a></p>
        </section>
    </main>
</body>
</html>

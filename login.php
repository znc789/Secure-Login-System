<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

$errors = [];
$identifier = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim((string)($_POST['identifier'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $csrfToken = (string)($_POST['csrf_token'] ?? '');

    if (!verifyCsrfToken($csrfToken)) {
        $errors[] = 'Invalid request. Please reload the page and try again.';
    }

    if ($identifier === '') {
        $errors[] = 'Please enter your email or username.';
    }

    if ($password === '') {
        $errors[] = 'Please enter your password.';
    }

    if (empty($errors)) {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT id, username, password_hash FROM users WHERE email = :identifier OR username = :identifier LIMIT 1');
        $stmt->execute(['identifier' => $identifier]);
        $user = $stmt->fetch();

        if ($user === false || !password_verify($password, (string)$user['password_hash'])) {
            $errors[] = 'Invalid credentials. Please check your email/username and password.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['username'] = (string)$user['username'];
            $_SESSION['last_activity'] = time();
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';

            header('Location: dashboard.php');
            exit;
        }
    }
}

$csrfToken = generateCsrfToken();
$flashMessage = getFlash();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In | Secure Login System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main class="container">
        <section class="card">
            <h1>Log In</h1>
            <?php if ($flashMessage): ?>
                <div class="alert success"><?= escape($flashMessage) ?></div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="alert error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= escape($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="login.php" novalidate>
                <input type="hidden" name="csrf_token" value="<?= escape($csrfToken) ?>">

                <label for="identifier">Email or Username</label>
                <input type="text" id="identifier" name="identifier" value="<?= escape($identifier) ?>" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Sign In</button>
            </form>

            <p class="footer-text">New here? <a href="register.php">Create an account</a></p>
        </section>
    </main>
</body>
</html>

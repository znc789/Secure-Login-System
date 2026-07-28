<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

$errors = [];
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $confirmPassword = (string)($_POST['confirm_password'] ?? '');
    $csrfToken = (string)($_POST['csrf_token'] ?? '');

    if (!verifyCsrfToken($csrfToken)) {
        $errors[] = 'Invalid request. Please reload the page and try again.';
    }

    if ($username === '' || !preg_match('/^[A-Za-z0-9_]{3,20}$/', $username)) {
        $errors[] = 'Username must be 3-20 characters and may contain letters, numbers, and underscores only.';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email OR username = :username LIMIT 1');
        $stmt->execute(['email' => $email, 'username' => $username]);

        if ($stmt->fetch()) {
            $errors[] = 'This email address or username is already registered.';
        }
    }

    if (empty($errors)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare('INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)');
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password_hash' => $passwordHash,
        ]);

        setFlash('Registration completed successfully. You can now log in.');
        header('Location: login.php');
        exit;
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
    <title>Register | Secure Login System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main class="container">
        <section class="card">
            <h1>Register</h1>
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

            <form method="post" action="register.php" novalidate>
                <input type="hidden" name="csrf_token" value="<?= escape($csrfToken) ?>">

                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= escape($username) ?>" required minlength="3" maxlength="20" pattern="[A-Za-z0-9_]+">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= escape($email) ?>" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="8">

                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="8">

                <button type="submit">Create Account</button>
            </form>

            <p class="footer-text">Already have an account? <a href="login.php">Log in</a></p>
        </section>
    </main>
</body>
</html>

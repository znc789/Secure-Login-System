<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

session_unset();
session_destroy();

setcookie(session_name(), '', time() - 3600, '/');

header('Location: login.php');
exit;

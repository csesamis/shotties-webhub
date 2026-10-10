<?php
// Shared page header. Set $pageTitle before including this file.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = $pageTitle ?? 'ShottiesHub';
$showNav   = $showNav ?? true;
$loggedIn  = isset($_SESSION['user']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?> | ShottiesHub</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header">
    <a class="brand" href="index.php">ShottiesHub</a>
<?php if ($showNav): ?>
    <nav class="site-nav" aria-label="Main navigation">
        <a href="index.php">Home</a>
        <a href="mainmenu.php">Menu</a>
<?php if ($loggedIn): ?>
        <a href="update_profile.php">View profile</a>
        <a class="btn btn-small" href="logout.php">Log out</a>
<?php else: ?>
        <a href="login.php">Log in</a>
        <a class="btn btn-small" href="registration.php">Register</a>
<?php endif; ?>
    </nav>
<?php endif; ?>
</header>
<main id="main">
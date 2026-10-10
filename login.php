<?php
session_start();
require 'functions.php';

// Already logged in: nothing to do here.
if (isset($_SESSION['user'])) {
    header('Location: mainmenu.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $match    = null;

    foreach (read_users() as $user) {
        if (isset($user['Email']) && strtolower($user['Email']) === $email) {
            $match = $user;
            break;
        }
    }

    // One generic message for every failure, so the page never reveals which emails are registered.
    $hash = $match['Password'] ?? '$2y$10$usesomesillystringforsaltuuQ6p9cJ1b3FfPqk0YlYlJ6w3x5C2Wm';
    if ($match !== null && is_string($password) && password_verify($password, $hash)) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'first_name' => $match['First Name'] ?? '',
            'last_name'  => $match['LastName'] ?? '',
            'dob'        => $match['DOB'] ?? '',
            'gender'     => $match['Gender'] ?? '',
            'email'      => $match['Email'],
            'hometown'   => $match['Hometown'] ?? '',
        ];
        header('Location: mainmenu.php');
        exit;
    }
    $error = 'Email or password is incorrect.';
}

// One-time message from registration ("Account created...").
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$pageTitle = 'Log in';
require 'header.php';
?>
<section class="page-intro">
    <h1>Log in</h1>
    <p>Welcome back, Shottie.</p>
</section>

<form class="form-card" action="login.php" method="post">
    <?php if ($flash): ?>
        <div class="notice" role="status"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="error-list" role="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" autocomplete="email" required autofocus>
    </div>
    <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" autocomplete="current-password" required>
    </div>

    <div class="form-actions">
        <button class="btn" type="submit">Log in</button>
    </div>
    <p class="form-note">New here? <a href="registration.php">Register</a></p>
</form>
<?php require 'footer.php'; ?>
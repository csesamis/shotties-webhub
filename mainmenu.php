<?php
$pageTitle = 'Main menu';
require 'header.php';

$name = $loggedIn ? htmlspecialchars($_SESSION['user']['first_name']) : null;

// Menu options: title, description, target page, optional tag, image
$options = [
    ['Catalogue',     'Browse albums, lightsticks, photocards and more, priced in RM.', 'catalog.php',       '',                         'img/pic1.jpeg'],
    ['Activities',    'Meetups and trading sessions. See dates, prices and venues.',   'activities.php',    '',                         'img/pic2.jpeg'],
    ['Community',     'See what other Shotties are collecting and sharing.',           'community.php',     '',                         'img/pic3.jpeg'],
    ['Trade Checker', 'Check whether a photocard trade is fair before you agree.',     'trade_check.php', 'Coming in Assignment 2', 'img/pic4.jpeg'],
];
?>
<section class="page-intro">
    <h1><?= $name ? "Welcome back, $name" : 'Main menu' ?></h1>
    <p><?= $loggedIn ? 'Pick where you want to go.' : 'You can browse without an account. Log in or register to book an activity.' ?></p>
</section>

<?php if (isset($_GET['login_required']) && !$loggedIn): ?>
<div class="notice" role="alert">
    <span>Please log in or register to book an activity.</span>
    <a class="btn btn-small" href="login.php">Log in</a>
    <a class="btn btn-small btn-outline" href="registration.php">Register</a>
</div>
<?php endif; ?>

<section class="menu-grid" aria-label="Site sections">
<?php foreach ($options as [$title, $desc, $link, $tag, $image]): ?>
    <a class="menu-card" href="<?= $link ?>">
        <img class="menu-card-image" src="<?= htmlspecialchars($image) ?>" alt="">
        <h2><?= htmlspecialchars($title) ?></h2>
        <p><?= htmlspecialchars($desc) ?></p>
        <?php if ($tag): ?><span class="tag"><?= htmlspecialchars($tag) ?></span><?php endif; ?>
    </a>
<?php endforeach; ?>
</section>
<?php require 'footer.php'; ?>
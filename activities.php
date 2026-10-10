<?php
$pageTitle = 'Activities';
require 'header.php';

require_once 'activities_data.php';
$activities = get_activities();
?>
<section class="page-intro">
    <h1>Activities</h1>
    <p>Meetups and workshops for Shotties in Kuching. <?= $loggedIn ? 'Pick one and register.' : 'Log in to register for an activity.' ?></p>
</section>

<section class="activity-list" aria-label="Upcoming activities">
<?php foreach ($activities as $a):
    $end = (clone $a['when'])->modify("+{$a['hours']} hours");
    // Logged-in users go to the booking form; everyone else goes to the main menu to log in or register.
    $registerLink = $loggedIn
        ? 'activity_reg.php?activity=' . urlencode($a['id'])
        : 'mainmenu.php?login_required=1';
?>
    <article class="activity-card">
        <div class="activity-head">
            <h2><?= htmlspecialchars($a['title']) ?></h2>
            <span class="tag"><?= htmlspecialchars($a['type']) ?></span>
        </div>
        <p><?= htmlspecialchars($a['about']) ?></p>
        <dl class="activity-details">
            <div><dt>Date</dt><dd><?= $a['when']->format('D, j M Y') ?></dd></div>
            <div><dt>Time</dt><dd><?= $a['when']->format('g:i A') ?> to <?= $end->format('g:i A') ?></dd></div>
            <div><dt>Price</dt><dd>RM <?= number_format($a['price'], 2) ?></dd></div>
            <div><dt>Venue</dt><dd><?= htmlspecialchars($a['venue']) ?></dd></div>
        </dl>
        <a class="btn" href="<?= $registerLink ?>"><?= $loggedIn ? 'Register' : 'Log in to register' ?></a>
    </article>
<?php endforeach; ?>
</section>
<?php require 'footer.php'; ?>
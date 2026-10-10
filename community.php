<?php
require_once 'functions.php';

$pageTitle = 'Community';
$contributions = read_contributions();
require 'header.php';
?>
<section class="page-intro">
    <h1>Community</h1>
    <p>See what other Shotties are collecting, making and sharing. Select a card to see the full post.</p>
</section>

<?php if (!$contributions): ?>
    <p class="empty-state">No contributions yet. Check back soon, or be the first to share once uploads open.</p>
<?php else: ?>
<section class="community-grid" aria-label="Community contributions">
    <?php foreach ($contributions as $c): ?>
        <a class="community-card" href="community_detail.php?id=<?= urlencode($c['ID'] ?? '') ?>">
            <?php render_media($c['Media'] ?? '', $c['Title'] ?? 'Contribution', 'community-media'); ?>
            <div class="community-body">
                <h2><?= htmlspecialchars($c['Title'] ?? '') ?></h2>
                <p>by <?= htmlspecialchars($c['Contributor'] ?? 'A Shottie') ?></p>
                <span class="tag"><?= htmlspecialchars($c['Related'] ?? '') ?></span>
            </div>
        </a>
    <?php endforeach; ?>
</section>
<?php endif; ?>
<?php require 'footer.php'; ?>
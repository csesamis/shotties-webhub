<?php
require_once 'functions.php';
require_once 'activities_data.php';
require_once 'catalog_data.php';

/**
 * Find the page a contribution's "related" label should link to:
 * an activity title -> activities.php, a catalogue item name -> its catalogue section.
 */
function find_related_link(string $related): ?string
{
    $wanted = strtolower($related);
    foreach (get_activities() as $activity) {
        if (strtolower($activity['title']) === $wanted) {
            return 'activities.php';
        }
    }
    foreach (get_catalog() as $category => $data) {
        foreach ($data['items'] as [$name]) {
            if (strtolower($name) === $wanted) {
                return 'catalog.php#' . slugify($category);
            }
        }
    }
    return null;
}

// Look up the contribution by its ID.
$id = $_GET['id'] ?? '';
$contribution = null;
foreach (read_contributions() as $c) {
    if (($c['ID'] ?? '') === $id) {
        $contribution = $c;
        break;
    }
}
if ($contribution === null) {
    http_response_code(404);
}

$pageTitle = $contribution['Title'] ?? 'Contribution not found';
require 'header.php';
?>
<?php if ($contribution === null): ?>
<section class="page-intro">
    <h1>Contribution not found</h1>
    <p>That post doesn't exist or may have been removed.</p>
    <p style="margin-top:1.5rem"><a class="btn" href="community.php">Back to community</a></p>
</section>
<?php else:
    $related = $contribution['Related'] ?? '';
    $link    = find_related_link($related);
?>
<article class="detail">
    <div class="detail-media">
        <?php render_media($contribution['Media'] ?? '', $contribution['Title'] ?? 'Contribution', 'detail-image'); ?>
    </div>
    <div class="detail-info">
        <h1><?= htmlspecialchars($contribution['Title'] ?? '') ?></h1>
        <dl class="activity-details">
            <div><dt>Contributor</dt><dd><?= htmlspecialchars($contribution['Contributor'] ?? 'A Shottie') ?></dd></div>
            <div>
                <dt>Related to</dt>
                <dd>
                    <?php if ($link): ?>
                        <a href="<?= htmlspecialchars($link) ?>"><?= htmlspecialchars($related) ?></a>
                    <?php else: ?>
                        <?= htmlspecialchars($related) ?>
                    <?php endif; ?>
                </dd>
            </div>
        </dl>
        <p><?= htmlspecialchars($contribution['Description'] ?? '') ?></p>
        <a class="btn btn-outline" href="community.php">Back to community</a>
    </div>
</article>
<?php endif; ?>
<?php require 'footer.php'; ?>
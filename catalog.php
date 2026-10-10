<?php
$pageTitle = 'Catalogue';

require_once 'catalog_data.php';
$categories = get_catalog();

require 'header.php';
?>
<section class="page-intro">
    <h1>Catalogue</h1>
    <p>Albums, fan gear and photocards from sellers in the community. All prices are in RM.</p>
    <nav class="jump-links" aria-label="Catalogue categories">
        <?php foreach ($categories as $category => $data): ?>
            <a href="#<?= slugify($category) ?>"><?= htmlspecialchars($category) ?></a>
        <?php endforeach; ?>
    </nav>
</section>

<?php foreach ($categories as $category => $data): ?>
<section class="catalog-section" id="<?= slugify($category) ?>">
    <h2><?= htmlspecialchars($category) ?></h2>
    <div class="item-grid">
    <?php foreach ($data['items'] as [$name, $note, $price]):
        $id  = slugify($name);
        $img = "img/catalog/$id.png";
        $imageFile = __DIR__ . '/' . $img;
    ?>
        <article class="item-card">
            <?php if (is_file($imageFile)): ?>
                <img class="item-image" src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($name) ?>" loading="lazy">
            <?php else: ?>
                <div class="item-image item-placeholder" aria-hidden="true"><?= $data['glyph'] ?></div>
            <?php endif; ?>
            <h3><?= htmlspecialchars($name) ?></h3>
            <p class="item-note"><?= htmlspecialchars($note) ?></p>
            <p class="item-price">RM <?= number_format($price, 2) ?></p>
            <a class="btn btn-small" href="order.php?item=<?= urlencode($id) ?>">Order</a>
        </article>
    <?php endforeach; ?>
    </div>
</section>
<?php endforeach; ?>
<?php require 'footer.php'; ?>
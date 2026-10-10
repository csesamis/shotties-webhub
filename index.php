<?php
$pageTitle = 'Home';
$showNav = false;

// Pick 3 random photocard images from the img folder (relative path).
$photos = glob('img/pic*.jpeg');
shuffle($photos);
$photos = array_slice($photos, 0, 3);

require 'header.php';
?>
<section class="hero">
    <div>
        <h1>Buy, trade and meet up with fellow Shotties.</h1>
        <p class="lead">ShottiesHub is a fan-run hub for LNGSHOT fans in Malaysia to shop merch and photocards, join local meetups and share their collections.</p>
        <p>Buying and trading merch online often means overpaying or getting scammed, and it is hard to find other fans nearby. ShottiesHub puts listings, meetups and member showcases in one place, so every Shottie can collect safely.</p>
        <div class="cta-row">
            <a class="btn" href="mainmenu.php">Main menu</a>
            <a class="btn btn-outline" href="login.php">Log in</a>
            <a class="btn btn-outline" href="registration.php">Register</a>
        </div>
    </div>
    <div class="card-stack">
        <?php foreach ($photos as $i => $photo): ?>
            <img src="<?= htmlspecialchars($photo) ?>" alt="Photocard sample <?= $i + 1 ?>">
        <?php endforeach; ?>
    </div>
</section>

<section class="how">
    <h2>What you can do here</h2>
    <ul>
        <li><strong>Browse merch</strong>Albums, lightsticks and photocards, all priced in RM.</li>
        <li><strong>Join meetups</strong>Book a spot at trading sessions and fan events near you.</li>
        <li><strong>Show your collection</strong>Share your binder or haul with the community.</li>
    </ul>
</section>
<?php require 'footer.php'; ?>
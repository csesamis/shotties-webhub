<?php
$pageTitle = 'Order';
$item = isset($_GET['item']) ? $_GET['item'] : '';
require 'header.php';
?>
<section class="page-intro">
    <h1>Ordering opens soon</h1>
    <p>The order page is coming in Assignment 2<?= $item !== '' ? ' (you picked: ' . htmlspecialchars($item) . ')' : '' ?>.</p>
    <p style="margin-top:1.5rem"><a class="btn" href="catalog.php">Back to catalogue</a></p>
</section>
<?php require 'footer.php'; ?>
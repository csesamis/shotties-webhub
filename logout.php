<?php
// End the session and return to the home page.
session_start();
$_SESSION = [];
session_destroy();
header('Location: index.php');
exit;
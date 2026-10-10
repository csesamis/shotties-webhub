<?php
session_start();
require 'functions.php';
require 'activities_data.php';

// Booking needs an account: send visitors to the main menu to log in or register.
if (!isset($_SESSION['user'])) {
    header('Location: mainmenu.php?login_required=1');
    exit;
}
$user = $_SESSION['user'];

$activities = get_activities();
$titles = array_column($activities, 'title');

/** Validate the booking form. Returns a list of error messages (empty = valid). */
function validate_booking(array $in, array $titles): array
{
    $errors = [];
    $labels = [
        'first_name' => 'First name', 'last_name' => 'Last name', 'contact' => 'Contact number',
        'email' => 'Email', 'when' => 'Activity date and time', 'title' => 'Activity title',
    ];
    foreach ($labels as $field => $label) {
        if ($in[$field] === '') {
            $errors[] = "$label is required.";
        }
    }
    foreach (['first_name' => 'First name', 'last_name' => 'Last name'] as $field => $label) {
        if ($in[$field] !== '' && !is_valid_name($in[$field])) {
            $errors[] = "$label can only contain letters and spaces (up to 50 characters).";
        }
    }
    // Digits with optional leading +, spaces and hyphens; 9 to 15 digits in total.
    if ($in['contact'] !== '') {
        $digits = preg_replace('/\D/', '', $in['contact']);
        if (!preg_match('/^\+?[0-9][0-9 \-]*$/', $in['contact']) || strlen($digits) < 9 || strlen($digits) > 15) {
            $errors[] = 'Contact number must have 9 to 15 digits (for example 012-345 6789).';
        }
    }
    if ($in['email'] !== '' && !filter_var($in['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email is not in a valid format (for example name@example.com).';
    }
    if ($in['when'] !== '') {
        $when = DateTime::createFromFormat('!Y-m-d\TH:i', $in['when']);
        if (!$when || $when->format('Y-m-d\TH:i') !== $in['when']) {
            $errors[] = 'Activity date and time is not valid.';
        } elseif ($when <= new DateTime()) {
            $errors[] = 'Activity date and time must be in the future.';
        }
    }
    if ($in['title'] !== '' && !in_array(strtolower($in['title']), array_map('strtolower', $titles), true)) {
        $errors[] = 'Activity title must match one of the listed activities.';
    }
    return $errors;
}

$errors = [];
$values = [
    'first_name' => $user['first_name'],
    'last_name'  => $user['last_name'],
    'contact'    => '',
    'email'      => $user['email'],
    'when'       => '',
    'title'      => '',
];

// Arriving from activities.php: pre-fill the chosen activity.
foreach ($activities as $a) {
    if (($_GET['activity'] ?? '') === $a['id']) {
        $values['title'] = $a['title'];
        $values['when']  = $a['when']->format('Y-m-d\TH:i');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $field => $_) {
        $posted = $_POST[$field] ?? '';
        $values[$field] = is_string($posted) ? trim($posted) : '';
    }
    $values['email'] = strtolower($values['email']);

    $errors = validate_booking($values, $titles);

    if (!$errors) {
        // Use the official spelling of the title so duplicates are detected reliably.
        $values['title'] = $titles[array_search(strtolower($values['title']), array_map('strtolower', $titles), true)];
        $when = DateTime::createFromFormat('!Y-m-d\TH:i', $values['when'])->format('d-m-Y H:i');

        $record = implode('|', [
            'First Name:' . $values['first_name'],
            'LastName:'   . $values['last_name'],
            'Contact:'    . $values['contact'],
            'Email:'      . $values['email'],
            'DateTime:'   . $when,
            'Activity:'   . $values['title'],
        ]);

        // One booking per email per activity.
        $result = append_record_if_unique(
            ACTIVITY_DIR,
            ACTIVITY_FILE,
            $record,
            fn(array $existing) => isset($existing['Email'], $existing['Activity'])
                && strtolower($existing['Email']) === $values['email']
                && strtolower($existing['Activity']) === strtolower($values['title'])
        );

        if ($result === 'saved') {
            $_SESSION['booking_done'] = ['title' => $values['title'], 'when' => $when, 'email' => $values['email']];
            header('Location: activity_reg.php?registered=1');
            exit;
        }
        $errors[] = $result === 'duplicate'
            ? $values['email'] . ' is already registered for ' . $values['title'] . '.'
            : 'Sorry, we could not save your registration. Please try again later.';
    }
}

// Success screen after the redirect above.
$done = null;
if (isset($_GET['registered']) && isset($_SESSION['booking_done'])) {
    $done = $_SESSION['booking_done'];
    unset($_SESSION['booking_done']);
}

$pageTitle = 'Activity registration';
require 'header.php';
?>
<?php if ($done): ?>
<section class="page-intro">
    <h1>You're registered</h1>
    <p>See you there, Shottie.</p>
</section>
<div class="form-card">
    <dl class="activity-details">
        <div><dt>Activity</dt><dd><?= htmlspecialchars($done['title']) ?></dd></div>
        <div><dt>Date and time</dt><dd><?= htmlspecialchars($done['when']) ?></dd></div>
        <div><dt>Booked for</dt><dd><?= htmlspecialchars($done['email']) ?></dd></div>
    </dl>
    <div class="form-actions">
        <a class="btn" href="activities.php">Back to activities</a>
        <a class="btn btn-outline" href="mainmenu.php">Main menu</a>
    </div>
</div>
<?php else: ?>
<section class="page-intro">
    <h1>Register for an activity</h1>
    <p>We've filled in your details. Check them and confirm your spot.</p>
</section>

<form class="form-card" action="activity_reg.php" method="post" novalidate>
    <?php if ($errors): ?>
        <div class="error-list" role="alert">
            <strong>Please fix the following:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="field-row">
        <div class="field">
            <label for="first_name">First name</label>
            <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($values['first_name']) ?>" autocomplete="given-name">
        </div>
        <div class="field">
            <label for="last_name">Last name</label>
            <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($values['last_name']) ?>" autocomplete="family-name">
        </div>
    </div>

    <div class="field-row">
        <div class="field">
            <label for="contact">Contact number</label>
            <input type="text" id="contact" name="contact" inputmode="tel" autocomplete="tel" value="<?= htmlspecialchars($values['contact']) ?>" placeholder="012-345 6789">
        </div>
        <div class="field">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" inputmode="email" autocomplete="email" value="<?= htmlspecialchars($values['email']) ?>">
        </div>
    </div>

    <div class="field">
        <label for="title">Activity title</label>
        <input type="text" id="title" name="title" list="activity-titles" value="<?= htmlspecialchars($values['title']) ?>" placeholder="Start typing or pick one">
        <datalist id="activity-titles">
            <?php foreach ($titles as $t): ?><option value="<?= htmlspecialchars($t) ?>"><?php endforeach; ?>
        </datalist>
    </div>

    <div class="field">
        <label for="when">Activity date and time</label>
        <input type="datetime-local" id="when" name="when" min="<?= date('Y-m-d\TH:i') ?>" value="<?= htmlspecialchars($values['when']) ?>">
    </div>

    <div class="form-actions">
        <button class="btn" type="submit">Confirm registration</button>
        <button class="btn btn-outline" type="reset">Reset</button>
        <a class="btn btn-outline" href="activities.php">Cancel</a>
    </div>
</form>
<?php endif; ?>
<?php require 'footer.php'; ?>
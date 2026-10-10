<?php
session_start();
require 'functions.php';

// Only logged-in members can edit a profile.
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
$email = $_SESSION['user']['email'];

const DEFAULT_AVATARS = [
    'Female' => 'img/profile_images/default_female.svg',
    'Male'   => 'img/profile_images/default_male.svg',
];

/** Find this member's stored record, or null if it no longer exists. */
function find_user(string $email): ?array
{
    foreach (read_users() as $user) {
        if (strtolower($user['Email'] ?? '') === strtolower($email)) {
            return $user;
        }
    }
    return null;
}

$stored = find_user($email);
if ($stored === null) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Form values start from the stored record (stored DOB is dd-mm-yyyy, the date input needs yyyy-mm-dd).
$dobDate = DateTime::createFromFormat('!d-m-Y', $stored['DOB'] ?? '');
$values = [
    'first_name' => $stored['First Name'] ?? '',
    'last_name'  => $stored['LastName'] ?? '',
    'dob'        => $dobDate ? $dobDate->format('Y-m-d') : '',
    'gender'     => $stored['Gender'] ?? 'Female',
    'hometown'   => $stored['Hometown'] ?? '',
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $field => $_) {
        $posted = $_POST[$field] ?? '';
        $values[$field] = is_string($posted) ? trim($posted) : '';
    }
    $password = is_string($_POST['password'] ?? '') ? ($_POST['password'] ?? '') : '';
    $confirm  = is_string($_POST['confirm_password'] ?? '') ? ($_POST['confirm_password'] ?? '') : '';

    foreach (['first_name' => 'First name', 'last_name' => 'Last name', 'dob' => 'Date of birth', 'hometown' => 'Hometown'] as $field => $label) {
        if ($values[$field] === '') {
            $errors[] = "$label is required.";
        }
    }
    foreach (['first_name' => 'First name', 'last_name' => 'Last name'] as $field => $label) {
        if ($values[$field] !== '' && !is_valid_name($values[$field])) {
            $errors[] = "$label can only contain letters and spaces (up to 50 characters).";
        }
    }
    if ($values['dob'] !== '' && ($message = dob_error($values['dob']))) {
        $errors[] = $message;
    }
    if (!isset(DEFAULT_AVATARS[$values['gender']])) {
        $errors[] = 'Please choose Male or Female.';
    }
    if (preg_match('/[|\r\n]/', $values['hometown']) || !preg_match('/^.{0,100}$/su', $values['hometown'])) {
        $errors[] = 'Hometown must be 100 characters or fewer and cannot contain the "|" character.';
    }
    // Password is optional here: leave it blank to keep the current one.
    $newHash = $stored['Password'] ?? '';
    if ($password !== '' || $confirm !== '') {
        if ($message = password_error($password)) {
            $errors[] = $message;
        } elseif ($password !== $confirm) {
            $errors[] = 'Confirm password does not match the password.';
        } else {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
        }
    }

    if (!$errors) {
        $record = implode('|', [
            'First Name:' . $values['first_name'],
            'LastName:'   . $values['last_name'],
            'DOB:'        . DateTime::createFromFormat('!Y-m-d', $values['dob'])->format('d-m-Y'),
            'Gender:'     . $values['gender'],
            'Email:'      . $stored['Email'],
            'Hometown:'   . $values['hometown'],
            'Password:'   . $newHash,
        ]);
        $saved = replace_record(
            USER_FILE,
            fn(array $existing) => strtolower($existing['Email'] ?? '') === strtolower($email),
            $record
        );
        if ($saved) {
            $_SESSION['user']['first_name'] = $values['first_name'];
            $_SESSION['user']['last_name']  = $values['last_name'];
            $_SESSION['user']['hometown']   = $values['hometown'];
            $_SESSION['user']['gender']     = $values['gender'];
            $_SESSION['user']['dob']        = DateTime::createFromFormat('!Y-m-d', $values['dob'])->format('d-m-Y');
            header('Location: update_profile.php?updated=1');
            exit;
        }
        $errors[] = 'Sorry, we could not save your changes. Please try again later.';
    }
}

// This member's activity registrations.
$bookings = array_values(array_filter(
    read_records(ACTIVITY_FILE),
    fn(array $b) => strtolower($b['Email'] ?? '') === strtolower($email)
));

$pageTitle = 'My profile';
require 'header.php';
?>
<section class="page-intro">
    <h1>My profile</h1>
    <p>View and update the details we have stored for you.</p>
</section>

<?php if (isset($_GET['updated'])): ?>
    <div class="notice" role="status">Your profile has been updated.</div>
<?php endif; ?>

<div class="profile-layout">
    <form class="form-card" action="update_profile.php" method="post" novalidate>
        <?php if ($errors): ?>
            <div class="error-list" role="alert">
                <strong>Please fix the following:</strong>
                <ul><?php foreach ($errors as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>

        <img id="avatar" class="profile-photo avatar" src="<?= DEFAULT_AVATARS[$values['gender']] ?? DEFAULT_AVATARS['Female'] ?>" alt="Default profile picture">

        <div class="field-row">
            <div class="field">
                <label for="first_name">First name</label>
                <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($values['first_name']) ?>">
            </div>
            <div class="field">
                <label for="last_name">Last name</label>
                <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($values['last_name']) ?>">
            </div>
        </div>

        <div class="field">
            <label for="email">Email (cannot be changed)</label>
            <input type="text" id="email" value="<?= htmlspecialchars($stored['Email']) ?>" readonly>
        </div>

        <div class="field">
            <label for="dob">Date of birth</label>
            <input type="date" id="dob" name="dob" min="1900-01-01" max="<?= date('Y-m-d', strtotime('-' . MIN_AGE . ' years')) ?>" value="<?= htmlspecialchars($values['dob']) ?>">
        </div>

        <fieldset class="field">
            <legend>Gender</legend>
            <?php foreach (array_keys(DEFAULT_AVATARS) as $g): ?>
                <label class="choice"><input type="radio" name="gender" value="<?= $g ?>" data-avatar="<?= DEFAULT_AVATARS[$g] ?>" <?= $values['gender'] === $g ? 'checked' : '' ?>> <?= $g ?></label>
            <?php endforeach; ?>
        </fieldset>

        <div class="field">
            <label for="hometown">Hometown</label>
            <input type="text" id="hometown" name="hometown" value="<?= htmlspecialchars($values['hometown']) ?>">
        </div>

        <div class="field-row">
            <div class="field">
                <label for="password">New password (optional)</label>
                <input type="password" id="password" name="password" autocomplete="new-password" aria-describedby="pw-hint">
                <small id="pw-hint">Leave blank to keep your current password.</small>
            </div>
            <div class="field">
                <label for="confirm_password">Confirm new password</label>
                <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password">
            </div>
        </div>

        <div class="form-actions">
            <button class="btn" type="submit">Update</button>
            <a class="btn btn-outline" href="mainmenu.php">Cancel</a>
        </div>
    </form>

    <section class="my-activity" aria-labelledby="my-activity-title">
        <h2 id="my-activity-title">My activity</h2>
        <?php if (!$bookings): ?>
            <p class="empty-state">No registrations yet. <a href="activities.php">Browse activities</a> to book your first one.</p>
        <?php else: ?>
            <ul class="booking-list">
                <?php foreach ($bookings as $b): ?>
                    <li><strong><?= htmlspecialchars($b['Activity'] ?? '') ?></strong><span><?= htmlspecialchars($b['DateTime'] ?? '') ?></span></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</div>

<script>
// Swap the default picture as soon as a different gender is chosen.
document.querySelectorAll('input[name="gender"]').forEach(function (radio) {
    radio.addEventListener('change', function () {
        document.getElementById('avatar').src = radio.dataset.avatar;
    });
});
</script>
<?php require 'footer.php'; ?>
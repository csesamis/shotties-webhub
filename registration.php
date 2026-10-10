<?php
$pageTitle = 'Register';
require 'header.php';

// Errors and previously entered values are left in the session by process_registration.php.
$errors = $_SESSION['reg_errors'] ?? [];
$old    = $_SESSION['reg_old'] ?? [];
unset($_SESSION['reg_errors'], $_SESSION['reg_old']);

/** Return a previously entered value, safe to print inside an HTML attribute. */
function old(array $old, string $key, string $default = ''): string
{
    return htmlspecialchars($old[$key] ?? $default);
}

$gender  = $old['gender'] ?? 'Female';
$maxDob  = date('Y-m-d', strtotime('-13 years')); // members must be at least 13
?>
<section class="page-intro">
    <h1>Create your account</h1>
    <p>Join ShottiesHub to book activities, trade and share your collection. You must be 13 or older.</p>
</section>

<form class="form-card" action="process_registration.php" method="post" novalidate>
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
            <input type="text" id="first_name" name="first_name" value="<?= old($old, 'first_name') ?>" autocomplete="given-name">
        </div>
        <div class="field">
            <label for="last_name">Last name</label>
            <input type="text" id="last_name" name="last_name" value="<?= old($old, 'last_name') ?>" autocomplete="family-name">
        </div>
    </div>

    <div class="field">
        <label for="dob">Date of birth</label>
        <input type="date" id="dob" name="dob" min="1900-01-01" max="<?= $maxDob ?>" value="<?= old($old, 'dob') ?>">
    </div>

    <fieldset class="field">
        <legend>Gender</legend>
        <label class="choice"><input type="radio" name="gender" value="Female" <?= $gender === 'Female' ? 'checked' : '' ?>> Female</label>
        <label class="choice"><input type="radio" name="gender" value="Male" <?= $gender === 'Male' ? 'checked' : '' ?>> Male</label>
    </fieldset>

    <div class="field">
        <label for="email">Email</label>
        <input type="text" id="email" name="email" inputmode="email" autocomplete="email" value="<?= old($old, 'email') ?>" placeholder="you@example.com">
    </div>

    <div class="field">
        <label for="hometown">Hometown</label>
        <input type="text" id="hometown" name="hometown" value="<?= old($old, 'hometown') ?>" placeholder="e.g. Kuching, Sarawak">
    </div>

    <div class="field-row">
        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" autocomplete="new-password" aria-describedby="pw-hint">
            <small id="pw-hint">At least 8 characters, with 1 number and 1 symbol.</small>
        </div>
        <div class="field">
            <label for="confirm_password">Confirm password</label>
            <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password">
        </div>
    </div>

    <div class="form-actions">
        <button class="btn" type="submit">Create account</button>
        <button class="btn btn-outline" type="reset">Reset</button>
    </div>
    <p class="form-note">Already have an account? <a href="login.php">Log in</a></p>
</form>
<?php require 'footer.php'; ?>
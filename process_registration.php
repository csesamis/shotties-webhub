<?php
session_start();
require 'functions.php';

/** Return a list of error messages for the submitted registration data (empty list = valid). */
function validate_registration(array $in): array
{
    $errors = [];

    // Req-1a: every field is mandatory.
    $labels = [
        'first_name' => 'First name', 'last_name' => 'Last name', 'dob' => 'Date of birth',
        'gender' => 'Gender', 'email' => 'Email', 'hometown' => 'Hometown',
        'password' => 'Password', 'confirm_password' => 'Confirm password',
    ];
    foreach ($labels as $field => $label) {
        if ($in[$field] === '') {
            $errors[] = "$label is required.";
        }
    }

    // Req-1b: names contain letters and spaces only.
    foreach (['first_name' => 'First name', 'last_name' => 'Last name'] as $field => $label) {
        if ($in[$field] !== '' && !is_valid_name($in[$field])) {
            $errors[] = "$label can only contain letters and spaces (up to 50 characters).";
        }
    }

    // Date of birth must be a real date, not in the future, and the member must be old enough.
    if ($in['dob'] !== '' && ($message = dob_error($in['dob']))) {
        $errors[] = $message;
    }

    if ($in['gender'] !== '' && !in_array($in['gender'], ['Male', 'Female'], true)) {
        $errors[] = 'Please choose Male or Female.';
    }

    // Req-1c: email format checked with PHP.
    if ($in['email'] !== '' && !filter_var($in['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email is not in a valid format (for example name@example.com).';
    }

    // "|" and line breaks would corrupt the data file format.
    if (preg_match('/[|\r\n]/', $in['hometown'])) {
        $errors[] = 'Hometown cannot contain the "|" character.';
    }
    if (!preg_match('/^.{0,100}$/su', $in['hometown'])) {
        $errors[] = 'Hometown must be 100 characters or fewer.';
    }

    // Req-1d: at least 8 characters, 1 number and 1 symbol.
    $pw = $in['password'];
    if ($pw !== '' && ($message = password_error($pw))) {
        $errors[] = $message;
    }

    // Req-1e: confirmation must match.
    if ($pw !== '' && $in['confirm_password'] !== '' && $pw !== $in['confirm_password']) {
        $errors[] = 'Confirm password does not match the password.';
    }

    return $errors;
}

/** Send the user back to the form with their errors and previous input (never the passwords). */
function back_to_form(array $errors, array $input): void
{
    unset($input['password'], $input['confirm_password']);
    $_SESSION['reg_errors'] = $errors;
    $_SESSION['reg_old']    = $input;
    header('Location: registration.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: registration.php');
    exit;
}

$fields = ['first_name', 'last_name', 'dob', 'gender', 'email', 'hometown', 'password', 'confirm_password'];
$input  = [];
foreach ($fields as $field) {
    $value = $_POST[$field] ?? '';
    $input[$field] = is_string($value) ? $value : '';
}
// Trim everything except passwords, where spaces may be intentional.
foreach (['first_name', 'last_name', 'dob', 'gender', 'email', 'hometown'] as $field) {
    $input[$field] = trim($input[$field]);
}
$input['email'] = strtolower($input['email']);

$errors = validate_registration($input);
if ($errors) {
    back_to_form($errors, $input);
}

// Build the record (Req-4): fields separated by "|", one record per line.
$dob = DateTime::createFromFormat('!Y-m-d', $input['dob'])->format('d-m-Y');
$record = implode('|', [
    'First Name:' . $input['first_name'],
    'LastName:'   . $input['last_name'],
    'DOB:'        . $dob,
    'Gender:'     . $input['gender'],
    'Email:'      . $input['email'],
    'Hometown:'   . $input['hometown'],
    'Password:'   . password_hash($input['password'], PASSWORD_DEFAULT),
]);

// Req-3: the helper creates the data directory only if it is missing, then checks and saves under a lock.
$result = append_record_if_unique(
    USER_DIR,
    USER_FILE,
    $record,
    fn(array $existing) => isset($existing['Email']) && strtolower($existing['Email']) === $input['email']
);
if ($result === 'duplicate') {
    back_to_form(['An account with this email already exists. Try logging in instead.'], $input);
}
if ($result === 'error') {
    back_to_form(['Sorry, we could not save your account. Please try again later.'], $input);
}

// Req-5: success, go to the login page.
$_SESSION['flash'] = 'Account created. Log in to continue.';
header('Location: login.php');
exit;
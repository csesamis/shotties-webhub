<?php
// Shared helpers for reading the user data file. Used by registration, login and profile pages.

const USER_DIR  = __DIR__ . '/data/User';
const USER_FILE = USER_DIR . '/user.txt';
const ACTIVITY_DIR  = __DIR__ . '/data/Activity';
const ACTIVITY_FILE = ACTIVITY_DIR . '/activity.txt';

/**
 * Turn one stored line into an array, e.g.
 * "First Name:John|LastName:Doe|..." => ['First Name' => 'John', 'LastName' => 'Doe', ...]
 */
function parse_record_line(string $line): array
{
    $record = [];
    foreach (explode('|', $line) as $part) {
        $pos = strpos($part, ':');
        if ($pos !== false) {
            $record[substr($part, 0, $pos)] = substr($part, $pos + 1);
        }
    }
    return $record;
}

/** Read every record from a data file (empty array if the file does not exist yet). */
function read_records(string $file): array
{
    if (!is_file($file)) {
        return [];
    }
    $users = [];
    $handle = fopen($file, 'r');
    if ($handle === false) {
        return [];
    }
    flock($handle, LOCK_SH);
    while (($line = fgets($handle)) !== false) {
        $line = trim($line);
        if ($line !== '') {
            $users[] = parse_record_line($line);
        }
    }
    flock($handle, LOCK_UN);
    fclose($handle);
    return $users;
}

/** Read every user record. */
function read_users(): array
{
    return read_records(USER_FILE);
}

/** True if the text contains only letters and spaces (1 to 50 characters). */
function is_valid_name(string $name): bool
{
    return (bool) preg_match('/^[\p{L} ]{1,50}$/u', $name);
}

/**
 * Append one record to a data file unless $isDuplicate says an existing record clashes with it.
 * The folder is only created if missing. The file is locked while it is checked and written.
 *
 * @param callable $isDuplicate receives each existing record (array) and returns true on a clash
 * @return string 'saved', 'duplicate' or 'error'
 */
function append_record_if_unique(string $dir, string $file, string $record, callable $isDuplicate): string
{
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        return 'error';
    }
    $handle = fopen($file, 'c+');
    if ($handle === false) {
        return 'error';
    }
    flock($handle, LOCK_EX);

    $duplicate = false;
    while (($line = fgets($handle)) !== false) {
        if ($isDuplicate(parse_record_line(trim($line)))) {
            $duplicate = true;
            break;
        }
    }
    if (!$duplicate) {
        fseek($handle, 0, SEEK_END);
        fwrite($handle, $record . PHP_EOL);
    }

    flock($handle, LOCK_UN);
    fclose($handle);
    return $duplicate ? 'duplicate' : 'saved';
}

const COMMUNITY_FILE = __DIR__ . '/data/Community/community.txt';

/** Read every stored community contribution (empty array if none are stored yet). */
function read_contributions(): array
{
    if (!is_file(COMMUNITY_FILE)) {
        return [];
    }
    $items = [];
    $handle = fopen(COMMUNITY_FILE, 'r');
    if ($handle === false) {
        return [];
    }
    flock($handle, LOCK_SH);
    while (($line = fgets($handle)) !== false) {
        $line = trim($line);
        if ($line !== '') {
            $items[] = parse_record_line($line);
        }
    }
    flock($handle, LOCK_UN);
    fclose($handle);
    return $items;
}

/** Print an <img> or <video> for a media path, depending on its file extension. */
function render_media(string $path, string $alt, string $class = ''): void
{
    $class = htmlspecialchars($class);
    $src   = htmlspecialchars($path);
    if (preg_match('/\.(mp4|webm|ogg)$/i', $path)) {
        echo "<video class=\"$class\" src=\"$src\" controls preload=\"metadata\" aria-label=\"" . htmlspecialchars($alt) . "\"></video>";
    } else {
        echo "<img class=\"$class\" src=\"$src\" alt=\"" . htmlspecialchars($alt) . "\" loading=\"lazy\">";
    }
}

const MIN_AGE = 13;

/** Return an error message if the password breaks the rules, otherwise null. */
function password_error(string $password): ?string
{
    if (strlen($password) < 8 || !preg_match('/\d/', $password) || !preg_match('/[^A-Za-z0-9\s]/', $password)) {
        return 'Password must be at least 8 characters and include 1 number and 1 symbol.';
    }
    return null;
}

/** Return an error message if a Y-m-d date of birth is invalid or too young, otherwise null. */
function dob_error(string $dob): ?string
{
    $date = DateTime::createFromFormat('!Y-m-d', $dob);
    if (!$date || $date->format('Y-m-d') !== $dob) {
        return 'Date of birth is not a valid date.';
    }
    if ($date > new DateTime('today')) {
        return 'Date of birth cannot be in the future.';
    }
    if ($date->diff(new DateTime('today'))->y < MIN_AGE) {
        return 'You must be at least ' . MIN_AGE . ' years old to register.';
    }
    return null;
}

/**
 * Replace the first record that $matches accepts with $newRecord, keeping every other line as it was.
 * The file is locked while it is rewritten.
 */
function replace_record(string $file, callable $matches, string $newRecord): bool
{
    $handle = fopen($file, 'c+');
    if ($handle === false) {
        return false;
    }
    flock($handle, LOCK_EX);

    $lines = [];
    $replaced = false;
    while (($line = fgets($handle)) !== false) {
        $line = rtrim($line, "\r\n");
        if ($line === '') {
            continue;
        }
        if (!$replaced && $matches(parse_record_line($line))) {
            $line = $newRecord;
            $replaced = true;
        }
        $lines[] = $line;
    }

    if ($replaced) {
        ftruncate($handle, 0);
        rewind($handle);
        fwrite($handle, implode(PHP_EOL, $lines) . PHP_EOL);
        fflush($handle);
    }
    flock($handle, LOCK_UN);
    fclose($handle);
    return $replaced;
}
<?php
$pageTitle = 'About';

// Paste the unlisted YouTube link here once the demo video is uploaded.
$videoUrl = '';

require 'header.php';
?>
<section class="page-intro">
    <h1>About ShottiesHub</h1>
    <p>What I built, why I built it, and what is still to come.</p>
</section>

<div class="about">
    <section>
        <h2>What problem does it solve, and why did I choose it?</h2>
        <ul>
            <li>Fans buying and trading K-pop merch online can overpay or get scammed, and it is hard to find other fans nearby. ShottiesHub brings merch listings, meetups and a community showcase into one place for LNGSHOT fans (Shotties) in Malaysia.</li>
            <li>I chose it because I am genuinely interested in the group, and that made me far more motivated to work on this assignment.</li>
            <li>I enjoyed learning and exploring what I could build, and choosing which pictures to use.</li>
            <li>I also hope it can help Shotties in Sarawak find each other, make friends and enjoy this fandom together.</li>
        </ul>
    </section>

    <section>
        <h2>Which PHP version is used?</h2>
        <ul>
            <li>PHP <?= htmlspecialchars(phpversion()) ?> (generated with <code>phpversion()</code>)</li>
        </ul>
    </section>

    <section>
        <h2>Which tasks have I completed?</h2>
        <ul>
            <li>Task 1: Home page (<code>index.php</code>) with an introduction, random photos and three buttons</li>
            <li>Task 2: Main menu (<code>mainmenu.php</code>) with a navigation bar, home and logout buttons, and four option cards</li>
            <li>Task 3: Catalogue (<code>catalog.php</code>) with 3 categories and 6 items in each</li>
            <li>Task 4: Activities (<code>activities.php</code>) showing type, date and time, price and venue</li>
            <li>Task 5: Community (<code>community.php</code>) listing stored contributions with a photo each</li>
            <li>Task 6: Contribution detail (<code>community_detail.php</code>)</li>
            <li>Task 7: Student profile (<code>profile.php</code>)</li>
            <li>Task 8: Update profile (<code>update_profile.php</code>), including default avatars by gender and a list of the member's activity registrations</li>
            <li>Task 9: Registration form (<code>registration.php</code>)</li>
            <li>Task 10: Process registration (<code>process_registration.php</code>) with validation and a unique email check</li>
            <li>Task 11: Activity registration (<code>activity_reg.php</code>) with validation and one booking per email per activity</li>
            <li>Task 12: Login (<code>login.php</code>)</li>
            <li>Task 13: This about page</li>
            <li>Task 14: Floating music player , i just wanted to have this.. (<code>player.php</code>)</li>
        </ul>
    </section>

    <section>
        <h2>Which tasks have I not attempted or not completed?</h2>
        <ul>
            <li>The order page that the catalogue buttons lead to is a placeholder, because it is built in Assignment 2.</li>
            <li>The Trade Checker (smart feature) is a placeholder page, reserved for Assignment 2.</li>
            <li>Uploading a community contribution is not available yet, also reserved for Assignment 2. The existing contributions are sample data.</li>
            <li>The demo video has not been recorded yet. Its link will be added below.</li>
        </ul>
    </section>

    <section>
        <h2>Which frameworks and third-party libraries did I use?</h2>
        <ul>
            <li>Embedded spotify player that loads from open.spotify.com. The site uses plain HTML5, CSS3, PHP and a small amount of vanilla JavaScript (swapping the default avatar when the gender changes).</li>
            <li>Fonts are the visitor's own system fonts, so nothing is loaded from outside.</li>
        </ul>
    </section>

    <section>
        <h2>Design decisions worth knowing</h2>
        <ul>
            <li>Passwords are stored as hashes using PHP's <code>password_hash()</code> and checked with <code>password_verify()</code>, so they are never saved as plain text. Password fields are masked on screen.</li>
            <li>Data files live inside the project, under <code>data/</code> (users, activity registrations and community posts), and use relative paths so the site works when moved.</li>
            <li>Login errors use one generic message, so the page never reveals whether an email is registered.</li>
            <li>The shared header, footer, data lists and file-handling functions are written once and reused across pages.</li>
        </ul>
    </section>

    <section>
        <h2>Video presentation</h2>
        <ul>
            <li>
                <?php if ($videoUrl !== ''): ?>
                    <a href="<?= htmlspecialchars($videoUrl) ?>" target="_blank" rel="noopener">Watch the demo video on YouTube</a>
                <?php else: ?>
                    <em>Demo video link coming soon.</em>
                <?php endif; ?>
            </li>
        </ul>
    </section>

    <p class="about-links">
        <a class="btn" href="index.php">Back to home page</a>
        <a class="btn btn-outline" href="profile.php">Student profile</a>
    </p>
</div>
<?php require 'footer.php'; ?>
<?php
$pageTitle = 'Student profile';

// Student details for this submission.
$student = [
    'name'  => 'Balqis Najwa BINTI IQBAL IKRAM',
    'id'    => '104395108',
    'email' => '104395108@students.swinburne.edu.my',
    'photo' => 'img/balqis.jpg',
];

require 'header.php';
?>
<section class="page-intro">
    <h1>Student profile</h1>
    <p>The person behind ShottiesHub.</p>
</section>

<section class="profile-card">
    <?php if (is_file($student['photo'])): ?>
        <img class="profile-photo" src="<?= htmlspecialchars($student['photo']) ?>" alt="Photo of <?= htmlspecialchars($student['name']) ?>">
    <?php else: ?>
        <div class="profile-photo profile-placeholder" aria-hidden="true">B</div>
    <?php endif; ?>
    <dl class="profile-details">
        <div><dt>Name</dt><dd><?= htmlspecialchars($student['name']) ?></dd></div>
        <div><dt>Student ID</dt><dd><?= htmlspecialchars($student['id']) ?></dd></div>
        <div><dt>Student email</dt><dd><a href="mailto:<?= htmlspecialchars($student['email']) ?>"><?= htmlspecialchars($student['email']) ?></a></dd></div>
    </dl>
</section>

<section class="declaration" aria-labelledby="declaration-title">
    <h2 id="declaration-title">Academic integrity declaration</h2>
    <p>Academic integrity is about taking responsibility for your learning and submitting work that is
    honestly your own. It means acknowledging the ideas, contributions and work of others;
    referencing your sources and acknowledging the use of generative artificial intelligence;
    contributing fairly to group work; and completing tasks, tests and exams without cheating.
    Artificial intelligence tools should only be used where approved by the Unit Convenor.
    Swinburne University uses the Turnitin system, which helps to identify inadequate citations,
    poor paraphrasing and unoriginal work in assignments that are submitted via Canvas. Your
    Unit Convenor will provide further details.</p>
    <p>Plagiarism, collusion, contract cheating, unauthorised file sharing, falsification, fabrication,
    manipulation or misrepresentation of information, reuse of previous work and non-compliance
    with instructions in an invigilated or non-invigilated assessment item are all breaches of
    academic integrity and treated as academic misconduct. Examples of breaches of academic
    integrity include, but are not limited to:</p>
    <ul>
        <li>submitting work as your own for assessment that has been fully or partially completed by a third party, either paid or unpaid</li>
        <li>using output from artificial intelligence tools (e.g. Co-Pilot) in whole or part without acknowledgement and/or without the approval of the Unit Convenor</li>
        <li>using another person’s work or ideas as though it is your own work, without appropriate attribution</li>
        <li>working closely with another student or group of students (either past or current), to submit for assessment, some or all of the other student or students’ work as your own work</li>
        <li>sharing without permission of the Unit Convenor, Swinburne resources or other material related to assessment to an entity or document repository site</li>
        <li>creating, intentionally modifying or inventing information that is intended to be submitted as part of an assessment item</li>
        <li>using the whole or part of a computer program written by another person as your own without appropriate acknowledgement</li>
        <li>poorly paraphrasing somebody else’s work</li>
        <li>using a musical composition or audio, visual, graphic and photographic work created by another person without acknowledgment</li>
        <li>enabling others to cheat, including letting another student copy your work or by giving access to a draft or completed assignment</li>
        <li>letting someone or something else impersonate you, or you impersonate someone else in an invigilated or non-invigilated assessment item</li>
        <li>accessing, obtaining and/or providing to others unauthorised materials relating to an invigilated or non-invigilated assessment item.</li>
    </ul>
</section>

<p class="profile-links">
    <a class="btn" href="index.php">Home page</a>
    <a class="btn btn-outline" href="about.php">About this project</a>
</p>
<?php require 'footer.php'; ?>
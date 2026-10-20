<?php

require 'config/app.php';


// 1. CHECK IF AN ADMINISTRATOR ALREADY EXISTS

$count = $db->query(
    'SELECT COUNT(*) n
     FROM admin'
)->fetch_assoc()['n'];


// Prevent setup if an administrator already exists
if ($count) {

    http_response_code(403);

    exit(
        'An admin already exists. Remove setup_admin.php from your deployed folder.'
    );

}


// Initialize error message
$err = '';


// 2. HANDLE ADMINISTRATOR SETUP FORM SUBMISSION

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verify CSRF token
    verify_csrf();

    // Get submitted administrator information
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';


    // 3. VALIDATE ADMINISTRATOR INFORMATION

    if (
        strlen($username) < 3 ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($pass) < 12
    ) {

        $err = 'Username must be 3+ characters, email valid, and password 12+ characters.';

    } else {


        // 4. HASH ADMINISTRATOR PASSWORD

        $hash = password_hash(
            $pass,
            PASSWORD_DEFAULT
        );


        // 5. INSERT NEW ADMINISTRATOR

        $st = $db->prepare(
            'INSERT INTO admin
                (username, email, password)
             VALUES (?, ?, ?)'
        );

        $st->bind_param(
            'sss',
            $username,
            $email,
            $hash
        );

        $st->execute();


        // 6. DISPLAY SUCCESS MESSAGE AND REDIRECT

        flash(
            'Administrator created. Delete setup_admin.php after setup.'
        );

        go('admin/login.php');

    }

}


// 7. SET PAGE TITLE AND INCLUDE HEADER

$title = 'First-time Admin Setup';

include 'includes/header.php';

?>


<!-- 8. ADMINISTRATOR SETUP SECTION -->

<section class="section wrap">

    <form method="post" class="form-card">


        <!-- FORM HEADING -->

        <h2>
            Create first administrator
        </h2>

        <p>
            One-time setup only. Delete this file after
            creating your administrator.
        </p>


        <!-- ERROR MESSAGE -->

        <?php if ($err): ?>

            <p class="error">
                <?= h($err) ?>
            </p>

        <?php endif; ?>


        <!-- CSRF TOKEN -->

        <input
            type="hidden"
            name="csrf"
            value="<?= csrf() ?>"
        >


        <!-- ADMINISTRATOR USERNAME -->

        <label>
            Username
        </label>

        <input
            name="username"
            required
            minlength="3"
        >


        <!-- ADMINISTRATOR EMAIL -->

        <label>
            Email
        </label>

        <input
            type="email"
            name="email"
            required
        >


        <!-- ADMINISTRATOR PASSWORD -->

        <label>
            Password (at least 12 characters)
        </label>

        <input
            type="password"
            name="password"
            required
            minlength="12"
        >


        <!-- CREATE ADMINISTRATOR BUTTON -->

        <p>

            <button class="btn dark">
                Create administrator
            </button>

        </p>

    </form>

</section>


<!-- 9. FOOTER -->

<?php include 'includes/footer.php'; ?>

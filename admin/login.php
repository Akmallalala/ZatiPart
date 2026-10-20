<?php

// 1. INCLUDE CONFIGURATION

require '../config/app.php';


// 2. REDIRECT ALREADY LOGGED-IN ADMINISTRATORS

if (!empty($_SESSION['admin_id'])) {

    go('dashboard.php');

}


// 3. INITIALIZE ERROR MESSAGE

$err = '';


// 4. HANDLE ADMIN LOGIN FORM

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 4.1 VERIFY CSRF TOKEN

    verify_csrf();


    // 4.2 GET USERNAME OR EMAIL

    $username = trim($_POST['username'] ?? '');


    // 4.3 FIND ADMINISTRATOR ACCOUNT

    $st = $db->prepare(
        'SELECT *
         FROM admin
         WHERE username = ? OR email = ?
         LIMIT 1'
    );

    $st->bind_param('ss', $username, $username);
    $st->execute();

    $a = $st->get_result()->fetch_assoc();


    // 4.4 VERIFY ADMINISTRATOR PASSWORD

    if (
        $a &&
        password_verify($_POST['password'] ?? '', $a['password'])
    ) {

        // Regenerate session ID for security
        session_regenerate_id(true);

        // Store administrator ID in session
        $_SESSION['admin_id'] = $a['admin_id'];

        // Redirect to admin dashboard
        go('dashboard.php');

    }


    // 4.5 DISPLAY INVALID LOGIN MESSAGE

    $err = 'Invalid administrator credentials.';

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <!-- 5. PAGE SETTINGS -->

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>BREWSIP Admin Login</title>


    <!-- 6. CSS STYLESHEET -->

    <link rel="stylesheet" href="../assets/style.css">

</head>

<body>

    <!-- 7. ADMIN LOGIN PAGE HEADER -->

    <div class="pagehead">

        <div class="wrap">

            <h1>BREWSIP Admin</h1>

        </div>

    </div>


    <!-- 8. ADMINISTRATOR LOGIN FORM -->

    <form class="form-card" method="post">

        <h2>Administrator sign in</h2>


        <!-- 8.1 DISPLAY LOGIN ERROR -->

        <?php if ($err): ?>

            <p class="error">
                <?= h($err) ?>
            </p>

        <?php endif; ?>


        <!-- 8.2 CSRF TOKEN -->

        <input
            type="hidden"
            name="csrf"
            value="<?= csrf() ?>"
        >


        <!-- 8.3 USERNAME OR EMAIL -->

        <label>Username or email</label>

        <input
            type="text"
            name="username"
            required
        >


        <!-- 8.4 PASSWORD -->

        <label>Password</label>

        <input
            type="password"
            name="password"
            required
        >


        <!-- 8.5 LOGIN BUTTON -->

        <p>

            <button class="btn dark">
                Login
            </button>

        </p>


        <!-- 8.6 RETURN TO CUSTOMER WEBSITE -->

        <a href="../index.php">
            ← Back to website
        </a>

    </form>

</body>

</html>

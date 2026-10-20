<?php

require 'config/app.php';

// Set page title
$title = 'Register';

// Initialize error message
$err = '';


// 1. HANDLE REGISTRATION FORM SUBMISSION

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verify CSRF token
    verify_csrf();

    // Get submitted registration information
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $pass = $_POST['password'] ?? '';


    // VALIDATE REGISTRATION INFORMATION

    if (
        strlen($name) < 2 ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        !preg_match('/^[0-9+() -]{6,20}$/', $phone) ||
        strlen($pass) < 8
    ) {

        $err = 'Enter a name, valid email and phone, and a password of at least 8 characters.';

    } else {


        //  CHECK FOR EXISTING CUSTOMER

        $st = $db->prepare(
            'SELECT customer_id
             FROM customer
             WHERE email = ?'
        );

        $st->bind_param('s', $email);
        $st->execute();


        // Check if email is already registered
        if ($st->get_result()->num_rows) {

            $err = 'This email is already registered.';

        } else {


            // HASH CUSTOMER PASSWORD

            $hash = password_hash(
                $pass,
                PASSWORD_DEFAULT
            );


            // INSERT NEW CUSTOMER

            $st = $db->prepare(
                'INSERT INTO customer
                    (name, email, phone, password)
                 VALUES (?, ?, ?, ?)'
            );

            $st->bind_param(
                'ssss',
                $name,
                $email,
                $phone,
                $hash
            );

            $st->execute();


            // CREATE CUSTOMER SESSION

            // Regenerate session ID for security
            session_regenerate_id(true);

            // Store customer information in session
            $_SESSION['customer_id'] = $db->insert_id;
            $_SESSION['customer_name'] = $name;


            // REDIRECT TO MENU

            go('menu.php');

        }

    }

}


// 2. INCLUDE HEADER

include 'includes/header.php';

?>


<!-- 3. PAGE HEADER -->

<div class="pagehead">

    <div class="wrap">

        <h1>
            Create Account
        </h1>

    </div>

</div>


<!-- 4. CUSTOMER REGISTRATION FORM -->

<section class="section wrap">

    <form class="form-card" method="post">


        <!-- REGISTRATION FORM HEADING -->

        <h2>
            Join BREWSIP
        </h2>


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


        <!-- FULL NAME -->

        <label>
            Full name
        </label>

        <input
            name="name"
            required
            maxlength="100"
            value="<?= h($_POST['name'] ?? '') ?>"
        >


        <!-- EMAIL ADDRESS -->

        <label>
            Email
        </label>

        <input
            type="email"
            name="email"
            required
            maxlength="100"
            value="<?= h($_POST['email'] ?? '') ?>"
        >


        <!-- PHONE NUMBER -->

        <label>
            Phone number
        </label>

        <input
            name="phone"
            required
            maxlength="20"
            value="<?= h($_POST['phone'] ?? '') ?>"
        >


        <!-- PASSWORD -->

        <label>
            Password (minimum 8 characters)
        </label>

        <input
            type="password"
            name="password"
            required
            minlength="8"
        >


        <!-- REGISTER BUTTON -->

        <p>

            <button class="btn dark">
                Register
            </button>

        </p>


        <!-- LOGIN LINK -->

        <p>
            Already registered?

            <a href="login.php">
                <u>Sign in</u>
            </a>
        </p>

    </form>

</section>


<!-- 5. FOOTER -->

<?php include 'includes/footer.php'; ?>

<?php

require 'config/app.php';

// Set page title
$title = 'Customer Login';

// Initialize error message
$err = '';

// Set allowed redirect pages
$allowedRedirects = [
    'orders.php',
    'cart.php',
    'checkout.php'
];

// Get the requested redirect page
$redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? 'orders.php';

// Validate redirect destination
if (!in_array($redirect, $allowedRedirects, true)) {
    $redirect = 'orders.php';
}

// Redirect customers who are already logged in
if (isset($_SESSION['customer_id'])) {
    go($redirect);
}

// 1. HANDLE LOGIN FORM SUBMISSION

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verify CSRF token
    verify_csrf();

    // Get submitted login credentials
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';


    // FETCH CUSTOMER INFORMATION

    $st = $db->prepare(
        'SELECT customer_id, name, password
         FROM customer
         WHERE email = ?
         LIMIT 1'
    );

    $st->bind_param('s', $email);
    $st->execute();

    $u = $st->get_result()->fetch_assoc();


    // VERIFY CUSTOMER CREDENTIALS

    if ($u && password_verify($pass, $u['password'])) {

        // Regenerate session ID for security
        session_regenerate_id(true);


        // Store customer information in session
        $_SESSION['customer_id'] = $u['customer_id'];
        $_SESSION['customer_name'] = $u['name'];


        // Redirect customer to their orders
        go($redirect);

    } else {

        // Display error if credentials are incorrect
        $err = 'Invalid email or password.';

    }

}

// 2. INCLUDE HEADER

include 'includes/header.php';

?>


<!-- 3. PAGE HEADER -->

<div class="pagehead">

    <div class="wrap">

        <h1>
            Customer Login
        </h1>

    </div>

</div>


<!-- 4. CUSTOMER LOGIN SECTION -->

<section class="section wrap">

    <form class="form-card" method="post">


        <!-- LOGIN FORM HEADING -->

        <h2>
            Welcome back
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


        <!-- Redirect Destination -->

        <input
            type="hidden"
            name="redirect"
            value="<?= h($redirect) ?>"
        >


        <!-- EMAIL ADDRESS -->

        <label>
            Email
        </label>

        <input
            type="email"
            name="email"
            required
            autocomplete="email"
        >


        <!-- PASSWORD -->

        <label>
            Password
        </label>

        <input
            type="password"
            name="password"
            required
            autocomplete="current-password"
        >


        <!-- SIGN IN BUTTON -->

        <p>

            <button class="btn dark">
                Sign in
            </button>

        </p>


        <!-- REGISTRATION LINK -->

        <p>
            New customer?

            <a href="register.php">
                <u>Create an account</u>
            </a>
        </p>

    </form>

</section>


<!-- 5. FOOTER -->

<?php include 'includes/footer.php'; ?>

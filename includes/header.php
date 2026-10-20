<?php

// 1. PAGE TITLE AND CURRENT PAGE

$title = $title ?? 'BREWSIP';
$current = basename($_SERVER['PHP_SELF']);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= h($title) ?> | BREWSIP</title>

    <!-- 2. CSS STYLESHEETS -->

    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/design.css">

    <!-- 2.1 REMIX ICONS -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/remixicon@4.9.1/fonts/remixicon.css"
    >

    <!-- 3. JAVASCRIPT -->

    <script src="assets/experience.js" defer></script>

</head>

<body>

    <!-- 4. HEADER AND NAVIGATION -->

    <header>

        <nav class="wrap nav new-nav">

            <!-- 4.1 WEBSITE LOGO -->

            <a class="logo" href="index.php">
                BREWSIP
            </a>


            <!-- 4.2 MOBILE NAVIGATION BUTTON -->

            <button
                class="mobile-toggle"
                type="button"
                aria-label="Toggle navigation"
                aria-expanded="false"
                aria-controls="mobile-nav"
            >
                <i class="ri-menu-line"></i>
            </button>


            <!-- 4.3 NAVIGATION LINKS -->

            <div class="links" id="mobile-nav">

                <a
                    class="<?= in_array($current, ['menu.php', 'drink.php']) ? 'active' : '' ?>"
                    href="menu.php"
                >
                    Menu
                </a>

                <a
                    class="<?= $current === 'about.php' ? 'active' : '' ?>"
                    href="about.php"
                >
                    About Us
                </a>

                <a
                    class="<?= $current === 'quiz.php' ? 'active' : '' ?>"
                    href="quiz.php"
                >
                    Quiz
                </a>

            </div>


            <!-- 4.4 CART AND ACCOUNT ICONS -->

            <div class="nav-actions">

                <!-- Shopping Cart -->

                <a
                    href="cart.php"
                    aria-label="Shopping cart"
                    title="Cart"
                >

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >

                        <circle cx="9" cy="21" r="1"/>
                        <circle cx="20" cy="21" r="1"/>

                        <path d="M1 2h3l3.5 14h13l2.5-10H5"/>

                    </svg>

                    <span class="cart-badge">
                        <?= cart_count() ?>
                    </span>

                </a>


                <!-- Customer Account -->

                <a
                    href="<?= logged() ? 'orders.php' : 'login.php' ?>"
                    aria-label="<?= logged() ? 'My account' : 'Log in' ?>"
                    title="Account"
                >

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >

                        <circle cx="12" cy="8" r="4"/>

                        <path d="M4 22v-2a8 8 0 0 1 16 0v2"/>

                    </svg>

                </a>

            </div>

        </nav>

    </header>


    <!-- 5. FLASH NOTIFICATION -->

    <?php if ($f = flash()): ?>

        <div class="notice wrap" role="status">
            <?= h($f) ?>
        </div>

    <?php endif; ?>


    <!-- 6. SHOPPING CART TOAST NOTIFICATION -->

    <div
        class="toast"
        id="cart-toast"
        role="status"
        aria-live="polite"
    ></div>


    <!-- 7. MAIN PAGE CONTENT -->

    <main>

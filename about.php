
<?php

require 'config/app.php';

// Set page title
$title = 'About Us';

// Include header
include 'includes/header.php';

?>


<!-- =========================================
     1. PAGE HEADER
========================================= -->

<div class="pagehead">

    <div class="wrap">

        <h1>
            About BREWSIP
        </h1>

        <p>
            Coffee made slowly, served with care.
        </p>

    </div>

</div>


<!-- =========================================
     2. ABOUT BREWSIP SECTION
========================================= -->

<section class="section wrap two">


    <!-- =====================================
         BREWSIP STORY
    ===================================== -->

    <div>

        <h2>
            From bean to cup
        </h2>

        <p>
            BREWSIP began with the belief that good coffee
            should be part of everyday life, not an
            occasional luxury.
        </p>

        <p>
            Browse our menu, place an order, and share
            your experience with a review.
        </p>

        <a href="menu.php" class="btn dark">
            Browse the menu
        </a>

    </div>


    <!-- =====================================
         2.2 ABOUT IMAGE
    ===================================== -->

    <img
        src="assets/img/about-coffee.png"
        alt="Coffee and beans"
    >

</section>


<!-- =========================================
     3. FOOTER
========================================= -->

<?php include 'includes/footer.php'; ?>

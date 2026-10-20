
<?php

require 'config/app.php';

// Set page title
$title = 'Home';


// =========================================
// 1. FETCH AVAILABLE MENU ITEMS
// =========================================

// $items = $db->query(
//     "SELECT m.*, c.category_name
//      FROM menu_item m
//      JOIN category c ON c.category_id = m.category_id
//      WHERE m.availability = 1
//      AND m.stock_quantity > 0
//      ORDER BY m.menu_id
//      LIMIT 10"
// )->fetch_all(MYSQLI_ASSOC);



$items = $db->query("
    SELECT m.*, c.category_name
    FROM menu_item m
    JOIN category c ON c.category_id = m.category_id
    WHERE m.availability = 1
      AND m.menu_name IN (
          'Spanish Latte',
          'Vietnamese Iced Coffee',
          'Dirty Matcha Latte',
          'Tiramisu Frappe',
          'Matcha Float',
          'Biscoff Latte',
          'Caramel Vanilla Latte',
          'Nutella Frappe'
      )
    ORDER BY FIELD(
        m.menu_name,
        'Spanish Latte',
        'Vietnamese Iced Coffee',
        'Dirty Matcha Latte',
        'Biscoff Latte',
        'Tiramisu Frappe',
        'Matcha Float',
        'Caramel Vanilla Latte',
        'Nutella Frappe'
    )
")->fetch_all(MYSQLI_ASSOC);


// =========================================
// 2. INCLUDE HEADER
// =========================================

include 'includes/header.php';

?>


<!-- =========================================
     3. HERO SECTION
========================================= -->

<section class="brew-hero">

    <!-- Hero Background Glow -->
    <div class="brew-glow" aria-hidden="true"></div>


    <!-- Hero Introduction -->
    <div class="wrap brew-intro reveal">

        <span class="brew-kicker">
            <span class="brew-spark">✳</span>
            brewLY BREWED · BREWSIP
        </span>

        <h1>
            YOUR DAILY DOSE
            <span>OF BREW.</span>
        </h1>

        <p>
            Good drinks, better moods. Explore your favourites,
            try something new, and find your perfect sip.
        </p>


        <!-- Hero Buttons -->
        <div class="brew-cta">

            <a class="brew-button" href="menu.php">
                Explore the menu
                <span>↗</span>
            </a>

            <a class="brew-button outline" href="quiz.php">
                Find my flavour
                <span>✳</span>
            </a>

        </div>

    </div>


    <!-- =====================================
         3.1 FEATURED DRINK GALLERY
    ===================================== -->

    <div
        class="wrap brew-gallery"
        aria-label="Discover our drinks"
    >

        <?php

        // Background colour palettes
        $palettes = [
            'peach',
            'lilac',
            'butter',
            'rose'
        ];

        ?>

        <?php foreach (array_slice($items, 0, 4) as $i => $m): ?>

            <a
                class="brew-drink-tile <?= $palettes[$i] ?> reveal"
                href="drink.php?id=<?= (int) $m['menu_id'] ?>"
            >

                <!-- Drink Number -->
                <span class="tile-number">
                    0<?= $i + 1 ?> / YOUR NEW FAVOURITE
                </span>


                <!-- Drink Image -->
                <div class="tile-photo">

                    <img
                        src="<?= h(item_picture($m)) ?>"
                        alt="<?= h($m['menu_name']) ?>"
                        loading="lazy"
                    >

                </div>


                <!-- Drink Information -->
                <div class="tile-bottom">

                    <div>

                        <strong>
                            <?= h($m['menu_name']) ?>
                        </strong>

                        <small>
                            <?= money($m['price']) ?>
                        </small>

                    </div>

                    <span
                        class="tile-arrow"
                        aria-hidden="true"
                    >
                        ↗
                    </span>

                </div>

            </a>

        <?php endforeach; ?>

    </div>


    <!-- =====================================
         3.2 HERO BOTTOM TEXT
    ===================================== -->

    <div class="brew-bottomline wrap">

        <span>
            ✦ MADE WITH CARE, SERVED WITH JOY
        </span>

        <span>
            SCROLL TO EXPLORE ↓
        </span>

    </div>

</section>


<!-- =========================================
     4. COFFEE TICKER SECTION
========================================= -->

<section
    class="brew-ticker"
    aria-label="Coffee moments"
>

    <div>
        SIP THE BLUE AWAY ✳
        YOUR CUP, YOUR WAY ✳
        SIP THE BLUE AWAY ✳
        YOUR CUP, YOUR WAY ✳
        SIP THE BLUE AWAY ✳
        YOUR CUP, YOUR WAY ✳
        SIP THE BLUE AWAY ✳
    </div>

</section>


<!-- =========================================
     5. FEATURE / ABOUT SECTION
========================================= -->

<section class="brew-feature">

    <div class="wrap brew-feature-grid">


        <!-- =====================================
             5.1 FEATURE IMAGE
        ===================================== -->

        <div class="brew-feature-art reveal">

            <span class="feature-stamp">
                THE GOOD STUFF<br>
                EVERY DAY ✳
            </span>

            <img
                src="assets/img/tiramisu.png"
                alt="Tiramisu Frappe"
                loading="lazy"
            >

        </div>


        <!-- =====================================
             5.2 FEATURE CONTENT
        ===================================== -->

        <div class="brew-feature-copy reveal">

            <span class="brew-kicker">
                01 / MORE THAN YOUR DAILY COFFEE
            </span>

            <h2>
                A LITTLE JOY IN
                <em>EVERY SIP.</em>
            </h2>

            <p>
                From creamy lattes to rich espresso and
                rebrewing matcha, there's a drink for every
                kind of day. Pick your favourite, make it yours,
                and enjoy the moment.
            </p>

            <a class="brew-button" href="about.php">
                Get to know us ↗
            </a>

        </div>

    </div>

</section>


<!-- =========================================
     6. CUSTOMER FAVOURITES SECTION
========================================= -->

<section class="favourites-section brew-favourites">

    <div class="wrap fav-wrap">


        <!-- =====================================
             6.1 SECTION HEADING
        ===================================== -->

        <div class="brew-section-heading reveal">

            <div>

                <span class="brew-kicker">
                    02 / THE CROWD PLEASERS
                </span>

                <h2>
                    THE MOST <em>LOVED.</em>
                </h2>

            </div>

            <p>
                Meet the drinks everyone keeps coming back for.
            </p>

        </div>


        <!-- =====================================
             6.2 FAVOURITES CAROUSEL
        ===================================== -->

        <div class="favourites-stage">


            <!-- Previous Drink Button -->
            <button
                type="button"
                class="fav-arrow"
                data-slide="-1"
                aria-label="Previous drinks"
            >
                ‹
            </button>


            <!-- Drinks Carousel -->
            <div
                class="favourites-track"
                id="favourites-track"
            >

                <?php foreach ($items as $m): ?>

                    <article class="fav-card">


                        <!-- Drink Image and Name -->
                        <a href="drink.php?id=<?= (int) $m['menu_id'] ?>">

                            <div class="fav-photo">

                                <img
                                    src="<?= h(item_picture($m)) ?>"
                                    alt="<?= h($m['menu_name']) ?>"
                                    loading="lazy"
                                >

                            </div>

                            <h3>
                                <?= h($m['menu_name']) ?>
                            </h3>

                        </a>


                        <!-- Drink Description -->
                        <p>
                            <?= h($m['description']) ?>
                        </p>


                        <!-- Drink Button and Price -->
                        <a
                            class="btn dark"
                            href="drink.php?id=<?= (int) $m['menu_id'] ?>"
                        >
                            Discover drink · <?= money($m['price']) ?>
                        </a>

                    </article>

                <?php endforeach; ?>

            </div>


            <!-- Next Drink Button -->
            <button
                type="button"
                class="fav-arrow"
                data-slide="1"
                aria-label="Next drinks"
            >
                ›
            </button>

        </div>


        <!-- Carousel Instructions -->
        <p class="fav-hint">
            SWIPE OR USE THE ARROWS TO EXPLORE MORE
        </p>

    </div>

</section>


<!-- COFFEE PERSONALITY QUIZ SECTION -->

<section class="brew-quiz-callout">

    <div class="wrap brew-quiz-grid">


        <!-- QUIZ INTRO (HOME) -->

        <div class="reveal">

            <span class="brew-kicker">
                03 / THE FLAVOUR FINDER
            </span>

            <h2>
                CAN'T DECIDE?<br>
                <em>WE GET IT.</em>
            </h2>

            <p>
                Sweet or bold? Iced or hot? Answer a few
                fun questions and we'll help you find your
                perfect coffee match.
            </p>

            <a href="quiz.php" class="brew-button">
                Take the coffee quiz ↗
            </a>

        </div>


        <!-- QUIZ IMAGE (HOME) -->

        <div class="brew-quiz-art reveal">

            <span>
                WHAT'S<br>
                YOUR<br>
                CUP?
            </span>

            <img
                src="assets/img/pistachio-matcha.png"
                alt="Pistachio Matcha"
                loading="lazy"
            >

        </div>

    </div>

</section>


<!-- CREATE ACCOUNT -->

<section class="brew-how">

    <div class="wrap">

        <span class="brew-kicker">
            04 / SIMPLE AS ONE, TWO, SIP
        </span>

        <h2>
            GOOD COFFEE, <em>YOUR WAY.</em>
        </h2>

        <p>
            Create a free account to order ahead, track your pickup and review your favourite drinks.
        </p>


        <!-- Create account button -->
        <a class="brew-button" href="register.php">
            Create Account ↗
        </a>

    </div>

</section>


<!-- WELCOME QUIZ POP UP -->

<dialog
    id="welcome-quiz"
    class="quiz-invite"
    aria-labelledby="invite-title"
>

    <!-- Close Popup Button -->
    <button
        class="invite-close"
        type="button"
        data-close-offer
        aria-label="Close"
    >
        ×
    </button>


    <!-- POP UP IMAGE -->

    <div class="invite-visual">

        <img
            src="assets/img/home-coffee.png"
            alt="brew coffee"
        >

    </div>


    <!-- POP UP UMAGE -->

    <div class="invite-content">

        <span class="eyebrow">
            YOUR NEXT FAVOURITE AWAITS
        </span>

        <h2 id="invite-title">
            Not sure what to sip?
        </h2>

        <p>
            Take our flavour-finder quiz and
            discover the drink that fits your mood.
        </p>


        <!-- Quiz Button -->
        <a href="quiz.php" class="btn dark">
            Find my coffee →
        </a>


        <!-- Maybe Later Button -->
        <button
            type="button"
            class="invite-later"
            data-close-offer
        >
            Maybe later
        </button>

    </div>

</dialog>


<!-- FOOTER -->

<?php include 'includes/footer.php'; ?>

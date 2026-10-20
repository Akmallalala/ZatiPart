
<?php

require 'config/app.php';

// Require customer to be logged in
require_user();

// Set page title
$title = 'Reviews';

// Initialize error message
$err = '';


// 1. HANDLE REVIEW FORM SUBMISSION

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verify CSRF token
    verify_csrf();

    // Get submitted review information
    $mid = (int) ($_POST['menu_id'] ?? 0);
    $rating = (int) ($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');


    // 2. CHECK IF SELECTED DRINK EXISTS

    $st = $db->prepare(
        'SELECT menu_id
         FROM menu_item
         WHERE menu_id = ?'
    );

    $st->bind_param('i', $mid);
    $st->execute();


    // 3. VALIDATE REVIEW INFORMATION

    if (
        !$st->get_result()->num_rows ||
        $rating < 1 ||
        $rating > 5 ||
        strlen($comment) > 1000
    ) {

        $err = 'Choose a drink, a rating from 1 to 5, and a comment under 1000 characters.';

    } else {

        // Get logged-in customer ID
        $cid = $_SESSION['customer_id'];


        // 4. INSERT CUSTOMER REVIEW

        $st = $db->prepare(
            'INSERT INTO reviews
                (customer_id, menu_id, rating, comment)
             VALUES (?, ?, ?, ?)'
        );

        $st->bind_param(
            'iiis',
            $cid,
            $mid,
            $rating,
            $comment
        );

        $st->execute();


        // Display success message
        flash('Thank you for your review!');

        // Redirect to reviews page
        go('reviews.php');

    }

}


// 5. FETCH AVAILABLE DRINKS

$items = $db->query(
    'SELECT menu_id, menu_name
     FROM menu_item
     ORDER BY menu_name'
);


// 6. FETCH RECENT CUSTOMER REVIEWS

$reviews = $db->query(
    'SELECT
        r.rating,
        r.comment,
        r.review_date,
        c.name,
        m.menu_name
     FROM reviews r
     JOIN customer c ON c.customer_id = r.customer_id
     JOIN menu_item m ON m.menu_id = r.menu_id
     ORDER BY r.review_date DESC
     LIMIT 30'
);


// 7. INCLUDE HEADER

include 'includes/header.php';

?>


<!-- 8. PAGE HEADER -->

<div class="pagehead">

    <div class="wrap">

        <h1>
            Customer Reviews
        </h1>

    </div>

</div>


<!-- 9. CUSTOMER REVIEWS SECTION -->

<section class="section wrap">


    <!-- REVIEW SUBMISSION FORM -->

    <form method="post" class="form-card">

        <h2>
            Share your experience
        </h2>


        <!-- Display Error Message -->

        <?php if ($err): ?>

            <p class="error">
                <?= h($err) ?>
            </p>

        <?php endif; ?>


        <!-- CSRF Token -->

        <input
            type="hidden"
            name="csrf"
            value="<?= csrf() ?>"
        >


        <!-- SELECT DRINK -->

        <label>
            Drink
        </label>

        <select name="menu_id" required>

            <?php foreach ($items as $m): ?>

                <option value="<?= $m['menu_id'] ?>">
                    <?= h($m['menu_name']) ?>
                </option>

            <?php endforeach; ?>

        </select>


        <!-- SELECT RATING -->

        <label>
            Rating
        </label>

        <select name="rating">

            <?php for ($i = 5; $i >= 1; $i--): ?>

                <option value="<?= $i ?>">
                    <?= $i ?> / 5
                </option>

            <?php endfor; ?>

        </select>


        <!-- REVIEW COMMENT -->

        <label>
            Comment
        </label>

        <textarea
            name="comment"
            maxlength="1000"
            rows="4"
        ></textarea>


        <!-- SUBMIT REVIEW BUTTON -->

        <p>

            <button class="btn dark">
                Submit review
            </button>

        </p>

    </form>


    <!-- 10. RECENT CUSTOMER REVIEWS -->

    <h2>
        Recent reviews
    </h2>

    <div class="grid">

        <?php foreach ($reviews as $r): ?>

            <div class="panel">


                <!-- Reviewed Drink Name -->

                <strong>
                    <?= h($r['menu_name']) ?>
                </strong>


                <!-- Customer Star Rating -->

                <p>
                    <?= str_repeat('★', (int) $r['rating']) ?>
                </p>


                <!-- Customer Review Comment -->

                <p>
                    <?= h($r['comment']) ?>
                </p>


                <!-- Customer Name and Review Date -->

                <p class="small muted">

                    By <?= h($r['name']) ?>

                    · <?= h($r['review_date']) ?>

                </p>

            </div>

        <?php endforeach; ?>

    </div>

</section>


<!-- 11. FOOTER -->

<?php include 'includes/footer.php'; ?>

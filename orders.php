
<?php

require 'config/app.php';

// Ensure the customer is logged in
require_user();

// Set page title
$title = 'My Orders';

// Get logged-in customer ID
$cid = $_SESSION['customer_id'];


// =========================================
// 1. FETCH CUSTOMER ORDERS
// =========================================

$st = $db->prepare(
    'SELECT *
     FROM orders
     WHERE customer_id = ?
     ORDER BY order_date DESC'
);

$st->bind_param('i', $cid);
$st->execute();

$orders = $st->get_result();


// =========================================
// 2. INCLUDE HEADER
// =========================================

include 'includes/header.php';

?>


<!-- =========================================
     3. PAGE HEADER
========================================= -->

<div class="pagehead">

    <div class="wrap">

        <h1>
            My Orders
        </h1>

    </div>

</div>


<!-- =========================================
     4. CUSTOMER ORDERS SECTION
========================================= -->

<section class="section wrap">


    <!-- CUSTOMER WELCOME MESSAGE -->
    <p>
        Welcome, <?= h($_SESSION['customer_name']) ?>.

        <a href="reviews.php">
            <u>Write a review</u>
        </a>
    </p>


    <!-- =====================================
         5. ORDERS TABLE
    ===================================== -->

    <div class="table-scroll">

        <table>

            <!-- TABLE HEADER -->
            <thead>

                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Items</th>
                </tr>

            </thead>


            <!-- TABLE BODY -->
            <tbody>

                <?php foreach ($orders as $o): ?>

                    <tr>

                        <!-- ORDER ID -->
                        <td>
                            #<?= $o['order_id'] ?>
                        </td>


                        <!-- ORDER DATE -->
                        <td>
                            <?= h($o['order_date']) ?>
                        </td>


                        <!-- TOTAL AMOUNT -->
                        <td>
                            <?= money($o['total_amount']) ?>
                        </td>


                        <!-- ORDER STATUS -->
                        <td>
                            <?= h($o['status']) ?>
                        </td>


                        <!-- ORDER ITEMS -->
                        <td>

                            <?php

                            // Fetch items belonging to this order
                            $st2 = $db->prepare(
                                'SELECT m.menu_name, oi.quantity
                                 FROM order_items oi
                                 JOIN menu_item m
                                 ON m.menu_id = oi.menu_id
                                 WHERE oi.order_id = ?'
                            );

                            $st2->bind_param('i', $o['order_id']);
                            $st2->execute();

                            // Display each ordered item
                            foreach ($st2->get_result() as $it) {

                                echo h($it['menu_name'])
                                    . ' × '
                                    . (int) $it['quantity']
                                    . '<br>';

                            }

                            ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</section>


<!-- =========================================
     6. FOOTER
========================================= -->

<?php include 'includes/footer.php'; ?>

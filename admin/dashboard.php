<?php

// 1. INCLUDE ADMIN CONFIGURATION

require 'common.php';


// 2. FETCH DASHBOARD STATISTICS

// 2.1 Total menu items
$products = $db->query(
    'SELECT COUNT(*) n FROM menu_item'
)->fetch_assoc()['n'];


// 2.2 Total orders
$orders = $db->query(
    'SELECT COUNT(*) n FROM orders'
)->fetch_assoc()['n'];


// 2.3 Non-cancelled order value
$revenue = $db->query(
    "SELECT COALESCE(SUM(total_amount), 0) n
     FROM orders
     WHERE status <> 'Cancelled'"
)->fetch_assoc()['n'];


// 3. DISPLAY ADMIN HEADER

admin_head('Dashboard');

?>


<!-- 4. ADMIN DASHBOARD -->

<section class="section wrap">

    <!-- 4.1 DASHBOARD HEADING -->

    <h1>Administrator Dashboard</h1>

    <p>
        Manage products, orders, and reports from one place.
    </p>


    <!-- 5. DASHBOARD SUMMARY -->

    <div class="summary-grid">

        <!-- 5.1 TOTAL MENU ITEMS -->

        <div class="panel">

            <strong><?= $products ?></strong>

            Menu items

        </div>


        <!-- 5.2 TOTAL ORDERS -->

        <div class="panel">

            <strong><?= $orders ?></strong>

            Total orders

        </div>


        <!-- 5.3 NON-CANCELLED ORDER VALUE -->

        <div class="panel">

            <strong><?= money($revenue) ?></strong>

            Non-cancelled order value

        </div>

    </div>


    <!-- 6. DASHBOARD ACTION BUTTONS -->

    <p>

        <a class="btn dark" href="items.php">
            Manage menu
        </a>

        <a class="btn" href="report.php">
            View report
        </a>

    </p>

</section>


<?php

// 7. DISPLAY ADMIN FOOTER

admin_foot();

?>
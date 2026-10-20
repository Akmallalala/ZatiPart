<?php

// 1. INCLUDE ADMIN CONFIGURATION

require 'common.php';


// 2. FETCH SALES SUMMARY

$summary = $db->query(
    "SELECT
        COUNT(*) orders_count,
        COALESCE(SUM(total_amount), 0) sales
     FROM orders
     WHERE status <> 'Cancelled'"
)->fetch_assoc();


// 3. FETCH TOP 10 MOST ORDERED DRINKS

$top = $db->query(
    "SELECT
        m.menu_name,
        SUM(oi.quantity) units,
        SUM(oi.subtotal) revenue
     FROM order_items oi
     JOIN menu_item m
        ON m.menu_id = oi.menu_id
     JOIN orders o
        ON o.order_id = oi.order_id
     WHERE o.status <> 'Cancelled'
     GROUP BY
        m.menu_id,
        m.menu_name
     ORDER BY units DESC
     LIMIT 10"
);


// 4. FETCH DAILY ORDER SUMMARY

$daily = $db->query(
    "SELECT
        DATE(order_date) day,
        COUNT(*) orders_count,
        SUM(total_amount) revenue
     FROM orders
     WHERE status <> 'Cancelled'
     GROUP BY DATE(order_date)
     ORDER BY day DESC
     LIMIT 30"
);


// 5. DISPLAY ADMIN HEADER

admin_head('Sales Report');

?>


<!-- 6. SALES REPORT SECTION -->

<section class="section wrap">

    <!-- 6.1 REPORT HEADING -->

    <h1>Sales Report</h1>

    <p class="muted">
        SQL aggregation from actual stored orders;
        cancelled orders excluded.
        These are order values, not confirmed payments.
    </p>


    <!-- 7. SALES SUMMARY -->

    <div class="summary-grid">

        <!-- 7.1 NON-CANCELLED ORDERS -->

        <div class="panel">

            <strong>
                <?= $summary['orders_count'] ?>
            </strong>

            Non-cancelled orders

        </div>


        <!-- 7.2 TOTAL ORDER VALUE -->

        <div class="panel">

            <strong>
                <?= money($summary['sales']) ?>
            </strong>

            Order value

        </div>


        <!-- 7.3 REPORT DATE -->

        <div class="panel">

            <strong>
                <?= date('d M Y') ?>
            </strong>

            Report date

        </div>

    </div>


    <!-- 8. MOST ORDERED DRINKS -->

    <h2>Most ordered drinks</h2>

    <div class="table-scroll">

        <table>

            <tr>
                <th>Drink</th>
                <th>Units sold</th>
                <th>Order value</th>
            </tr>


            <!-- 8.1 DISPLAY TOP 10 DRINKS -->

            <?php foreach ($top as $r): ?>

                <tr>

                    <!-- Drink Name -->

                    <td>
                        <?= h($r['menu_name']) ?>
                    </td>


                    <!-- Units Sold -->

                    <td>
                        <?= $r['units'] ?>
                    </td>


                    <!-- Order Value -->

                    <td>
                        <?= money($r['revenue']) ?>
                    </td>

                </tr>

            <?php endforeach; ?>

        </table>

    </div>


    <!-- 9. DAILY ORDERS -->

    <h2>Daily orders (last 30 active days)</h2>

    <div class="table-scroll">

        <table>

            <tr>
                <th>Date</th>
                <th>Orders</th>
                <th>Value</th>
            </tr>


            <!-- 9.1 DISPLAY DAILY ORDER RECORDS -->

            <?php foreach ($daily as $r): ?>

                <tr>

                    <!-- Order Date -->

                    <td>
                        <?= h($r['day']) ?>
                    </td>


                    <!-- Number of Orders -->

                    <td>
                        <?= $r['orders_count'] ?>
                    </td>


                    <!-- Daily Order Value -->

                    <td>
                        <?= money($r['revenue']) ?>
                    </td>

                </tr>

            <?php endforeach; ?>

        </table>

    </div>

</section>


<?php

// 10. DISPLAY ADMIN FOOTER

admin_foot();

?>

<?php

// 1. INCLUDE ADMIN CONFIGURATION

require 'common.php';


// 2. HANDLE ORDER STATUS UPDATE

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 2.1 VERIFY CSRF TOKEN

    verify_csrf();


    // 2.2 GET ORDER DETAILS

    $id = (int) ($_POST['order_id'] ?? 0);

    $status = $_POST['status'] ?? '';


    // 2.3 VALIDATE ORDER STATUS

    if (
        in_array(
            $status,
            [
                'Pending',
                'Preparing',
                'Ready',
                'Completed',
                'Cancelled'
            ],
            true
        )
    ) {

        // 2.4 UPDATE ORDER STATUS IN DATABASE

        $st = $db->prepare(
            'UPDATE orders
             SET status = ?
             WHERE order_id = ?'
        );

        $st->bind_param('si', $status, $id);

        $st->execute();


        // 2.5 DISPLAY SUCCESS MESSAGE

        flash('Order status updated.');

    }


    // 2.6 REDIRECT TO ORDERS PAGE

    go('orders.php');

}


// 3. FETCH CUSTOMER ORDERS

$orders = $db->query(
    'SELECT
        o.*,
        c.name
     FROM orders o
     JOIN customer c
        ON c.customer_id = o.customer_id
     ORDER BY o.order_date DESC'
);


// 4. DISPLAY ADMIN HEADER

admin_head('Orders');

?>


<!-- 5. CUSTOMER ORDERS SECTION -->

<section class="section wrap">

    <!-- 5.1 PAGE HEADING -->

    <h1>Customer Orders</h1>


    <!-- 6. CUSTOMER ORDERS TABLE -->

    <div class="table-scroll">

        <table>

            <!-- 6.1 TABLE HEADINGS -->

            <tr>
                <th>Order</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
                <th>Update status</th>
            </tr>


            <!-- 6.2 DISPLAY CUSTOMER ORDERS -->

            <?php foreach ($orders as $o): ?>

                <tr>

                    <!-- Order ID -->

                    <td>
                        #<?= $o['order_id'] ?>
                    </td>


                    <!-- Customer Name -->

                    <td>
                        <?= h($o['name']) ?>
                    </td>


                    <!-- Order Date -->

                    <td>
                        <?= h($o['order_date']) ?>
                    </td>


                    <!-- Order Total -->

                    <td>
                        <?= money($o['total_amount']) ?>
                    </td>


                    <!-- Current Order Status -->

                    <td>
                        <?= h($o['status']) ?>
                    </td>


                    <!-- 7. UPDATE ORDER STATUS -->

                    <td>

                        <form method="post" class="actions">

                            <!-- 7.1 CSRF TOKEN -->

                            <input
                                type="hidden"
                                name="csrf"
                                value="<?= csrf() ?>"
                            >


                            <!-- 7.2 ORDER ID -->

                            <input
                                type="hidden"
                                name="order_id"
                                value="<?= $o['order_id'] ?>"
                            >


                            <!-- 7.3 ORDER STATUS OPTIONS -->

                            <select
                                name="status"
                                style="width:auto"
                            >

                                <?php foreach (
                                    [
                                        'Pending',
                                        'Preparing',
                                        'Ready',
                                        'Completed',
                                        'Cancelled'
                                    ] as $s
                                ): ?>

                                    <option
                                        <?= $o['status'] === $s ? 'selected' : '' ?>
                                    >
                                        <?= $s ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>


                            <!-- 7.4 UPDATE BUTTON -->

                            <button class="btn">
                                Update
                            </button>

                        </form>

                    </td>

                </tr>

            <?php endforeach; ?>

        </table>

    </div>

</section>


<?php

// 8. DISPLAY ADMIN FOOTER

admin_foot();

?>

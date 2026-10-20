
<?php

require 'config/app.php';

// Set page title
$title = 'Checkout';

// Initialize variables
$err = '';
$success = false;
$orderId = 0;
$orderTotal = 0;
$discount = 0;
$code = '';


// 1. HANDLE CHECKOUT FORM SUBMISSION

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verify CSRF token
    verify_csrf();

    // Get customer information
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Get promotional code
    $code = strtoupper(trim($_POST['promo'] ?? ''));

    // 1.1 VALIDATE CHECKOUT INFORMATION

    if (empty($_SESSION['cart'])) {

        $err = 'Your cart is empty.';

    } elseif (
        !logged() &&
        (
            mb_strlen($name) < 2 ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            !preg_match('/^[+0-9 ()-]{7,20}$/', $phone)
        )
    ) {

        $err = 'Enter your name, valid email and phone number.';

    } elseif ($code !== '' && $code !== 'BREW10') {

        $err = 'This promotional code is not valid.';

    } else {

        try {

            // 2. START DATABASE TRANSACTION

            $db->begin_transaction();

            $rows = [];
            $total = 0;

            // VALIDATE CART ITEMS AND STOCK

            foreach ($_SESSION['cart'] as $id => $qty) {

                $id = (int) $id;
                $qty = (int) $qty;

                // Validate quantity
                if ($qty < 1) {
                    throw new Exception('Invalid quantity.');
                }


                // Fetch item and lock row
                $st = $db->prepare(
                    'SELECT
                        menu_id,
                        menu_name,
                        price,
                        stock_quantity,
                        availability
                     FROM menu_item
                     WHERE menu_id = ?
                     FOR UPDATE'
                );

                $st->bind_param('i', $id);
                $st->execute();

                $m = $st->get_result()->fetch_assoc();


                // Check item availability and stock
                if (
                    !$m ||
                    !$m['availability'] ||
                    $m['stock_quantity'] < $qty
                ) {

                    throw new Exception(
                        'Some items are out of stock. Please update your cart.'
                    );

                }


                // Calculate item subtotal
                $m['qty'] = $qty;

                $m['subtotal'] = round(
                    $m['price'] * $qty,
                    2
                );

                // Calculate total
                $total += $m['subtotal'];

                // Store validated item
                $rows[] = $m;

            }

            // 3. CUSTOMER INFORMATION

            // Get logged-in customer ID
            $cid = (int) ($_SESSION['customer_id'] ?? 0);


            // CREATE GUEST CUSTOMER

            if (!$cid) {

                // Generate random password
                $password = password_hash(
                    bin2hex(random_bytes(24)),
                    PASSWORD_DEFAULT
                );


                // Insert guest customer record
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
                    $password
                );

                $st->execute();

                // Get new customer ID
                $cid = $db->insert_id;

            }


            // 4. APPLY PROMOTIONAL DISCOUNT

            $discount = $code === 'BREW10'
                ? round($total * .1, 2)
                : 0;

            // Calculate final order total
            $orderTotal = round(
                $total - $discount,
                2
            );


            // 5. CREATE CUSTOMER ORDER

            $st = $db->prepare(
                'INSERT INTO orders
                    (customer_id, total_amount, status)
                 VALUES (?, ?, "Pending")'
            );

            $st->bind_param(
                'id',
                $cid,
                $orderTotal
            );

            $st->execute();

            // Get newly created order ID
            $orderId = $db->insert_id;


            // 6. SAVE ORDER ITEMS

            foreach ($rows as $m) {

                // Insert ordered drink
                $st = $db->prepare(
                    'INSERT INTO order_items
                        (
                            order_id,
                            menu_id,
                            quantity,
                            unit_price,
                            subtotal
                        )
                     VALUES (?, ?, ?, ?, ?)'
                );

                $st->bind_param(
                    'iiidd',
                    $orderId,
                    $m['menu_id'],
                    $m['qty'],
                    $m['price'],
                    $m['subtotal']
                );

                $st->execute();


                // UPDATE DRINK STOCK

                $st = $db->prepare(
                    'UPDATE menu_item
                     SET stock_quantity = stock_quantity - ?
                     WHERE menu_id = ?'
                );

                $st->bind_param(
                    'ii',
                    $m['qty'],
                    $m['menu_id']
                );

                $st->execute();

            }


            // 7. COMPLETE ORDER TRANSACTION

            // Commit database changes
            $db->commit();

            // Clear cart and customization
            unset(
                $_SESSION['cart'],
                $_SESSION['custom']
            );

            // Mark checkout as successful
            $success = true;



        // 8. HANDLE CHECKOUT ERRORS

        } catch (Throwable $e) {

            // Roll back database changes
            $db->rollback();

            // Display appropriate error message
            $err = $e instanceof mysqli_sql_exception
                ? 'Unable to place order. Check whether your email is already registered; you can sign in instead.'
                : $e->getMessage();

        }

    }

}


// 9. INCLUDE HEADER

include 'includes/header.php';

?>


<!-- 10. PAGE HEADER -->

<div class="pagehead">

    <div class="wrap">

        <h1>
            Checkout
        </h1>

    </div>

</div>


<!-- 11. CHECKOUT SECTION -->

<section class="section wrap">

    <div class="form-card">


        <!-- ORDER SUCCESS MESSAGE -->

        <?php if ($success): ?>

            <h2>
                Order placed!
            </h2>

            <p>
                Your order
                <strong>#<?= $orderId ?></strong>
                has been recorded as Pending.
            </p>


            <!-- ORDER TOTAL -->
            <p>

                Total:
                <strong>
                    <?= money($orderTotal) ?>
                </strong>

                <?php if ($discount): ?>

                    (including <?= money($discount) ?> discount)

                <?php endif; ?>

            </p>


            <!-- PAYMENT INFORMATION -->
            <p>
                Please keep your order number for reference.
                Payment is not processed online.
            </p>


            <!-- BACK TO MENU -->
            <a class="btn dark" href="menu.php">
                Back to menu
            </a>


        <!-- CHECKOUT FORM -->

        <?php else: ?>

            <h2>
                Confirm your order
            </h2>


            <!-- ERROR MESSAGE -->
            <?php if ($err): ?>

                <p class="error" role="alert">
                    <?= h($err) ?>
                </p>

            <?php endif; ?>


            <!-- CHECKOUT INFORMATION -->
            <p>
                Order as a guest or sign in to your account.
                No online payment is collected.
            </p>


            <!-- 12. CUSTOMER LOGIN STATUS -->

            <?php if (!logged()): ?>

                <p>
                    <a href="login.php">
                        Already have an account? Log in →
                    </a>
                </p>

            <?php else: ?>

                <p>
                    Signed in as
                    <strong>
                        <?= h($_SESSION['customer_name'] ?? 'Customer') ?>
                    </strong>.
                </p>

            <?php endif; ?>


            <!-- 13. CHECKOUT FORM FIELDS -->

            <form method="post">

                <!-- CSRF Token -->
                <input
                    type="hidden"
                    name="csrf"
                    value="<?= csrf() ?>"
                >


                <!-- GUEST CUSTOMER DETAILS -->

                <?php if (!logged()): ?>


                    <!-- FULL NAME -->
                    <label for="guest-name">
                        Full name
                    </label>

                    <input
                        id="guest-name"
                        name="name"
                        value="<?= h($_POST['name'] ?? '') ?>"
                        minlength="2"
                        maxlength="100"
                        required
                    >


                    <!-- EMAIL ADDRESS -->
                    <label for="guest-email">
                        Email
                    </label>

                    <input
                        id="guest-email"
                        type="email"
                        name="email"
                        value="<?= h($_POST['email'] ?? '') ?>"
                        required
                    >


                    <!-- PHONE NUMBER -->
                    <label for="guest-phone">
                        Phone number
                    </label>

                    <input
                        id="guest-phone"
                        type="tel"
                        name="phone"
                        value="<?= h($_POST['phone'] ?? '') ?>"
                        maxlength="20"
                        required
                    >

                <?php endif; ?>


                <!-- PROMOTIONAL CODE -->

                <label for="promo">
                    Promo code (optional)
                </label>

                <input
                    id="promo"
                    name="promo"
                    value="<?= h($_POST['promo'] ?? '') ?>"
                    placeholder="BREW10"
                >


                <!-- PROMOTIONAL INFORMATION -->
                <p class="small">
                    BREW10 gives 10% off the order total.
                    Guest orders are stored using a guest
                    customer record for reporting.
                </p>


                <!-- PLACE ORDER BUTTON -->

                <button
                    class="btn dark"
                    <?= empty($_SESSION['cart']) ? 'disabled' : '' ?>
                >
                    Place pickup order
                </button>

            </form>

        <?php endif; ?>

    </div>

</section>


<!-- 14. FOOTER -->

<?php include 'includes/footer.php'; ?>

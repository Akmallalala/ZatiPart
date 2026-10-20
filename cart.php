<?php

require 'config/app.php';

// Set page title
$title = 'Your Cart';


// 1. PRICING AND CUSTOMIZATION HELPERS

// Calculate customized drink price.
// All prices are calculated on the server.
function drink_unit_price($basePrice, $custom)
{
    $sizePrices = [
        'Small' => -0.50,
        'Medium' => 0.00,
        'Large' => 0.50
    ];

    $milkPrices = [
        '2% Milk' => 0.00,
        'Whole Milk' => 0.00,
        'No milk' => 0.00,
        'Oat Milk' => 0.80,
        'Almond Milk' => 0.80,
        'Soy Milk' => 0.80
    ];

    $size = $custom['size'] ?? 'Medium';
    $milk = $custom['milk'] ?? '2% Milk';

    return round(
        max(
            0,
            (float) $basePrice
            + ($sizePrices[$size] ?? 0)
            + ($milkPrices[$milk] ?? 0)
        ),
        2
    );
}


// Check whether a drink is hot.
function is_hot_drink($name)
{
    return stripos(trim((string) $name), 'Hot ') === 0;
}


// Validate a submitted customization.
function drink_customization($post, $drinkName)
{
    $sizes = ['Small', 'Medium', 'Large'];

    $milks = [
        '2% Milk',
        'Whole Milk',
        'No milk',
        'Oat Milk',
        'Almond Milk',
        'Soy Milk'
    ];

    $extras = [
        'Ice',
        'Extra ice',
        'No ice'
    ];

    $toppings = [
        'None',
        'Caramel drizzle',
        'Chocolate sauce',
        'Whipped cream'
    ];

    $size = $post['size'] ?? 'Medium';
    $milk = $post['milk'] ?? '2% Milk';
    $extra = $post['extra'] ?? 'Ice';
    $topping = $post['topping'] ?? 'None';

    if (!in_array($size, $sizes, true)) {
        $size = 'Medium';
    }

    if (!in_array($milk, $milks, true)) {
        $milk = '2% Milk';
    }

    if (!in_array($extra, $extras, true)) {
        $extra = 'Ice';
    }

    if (!in_array($topping, $toppings, true)) {
        $topping = 'None';
    }

    // Hot drinks cannot have ice.
    if (is_hot_drink($drinkName)) {
        $extra = 'None';
    }

    return [
        'size' => $size,
        'milk' => $milk,
        'extra' => $extra,

        'shots' => max(
            0,
            min(6, (int) ($post['shots'] ?? 2))
        ),

        'pumps' => max(
            0,
            min(8, (int) ($post['pumps'] ?? 2))
        ),

        'topping' => $topping,

        'note' => substr(
            (string) ($post['note'] ?? ''),
            0,
            80
        )
    ];
}



// 2. INITIALIZE CART SESSION

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!isset($_SESSION['custom']) || !is_array($_SESSION['custom'])) {
    $_SESSION['custom'] = [];
}


// 3. HANDLE CART FORM SUBMISSIONS

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $id = (int) ($_POST['menu_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $msg = 'Cart updated.';
    $ok = true;


    // ADD DRINK TO CART

    if ($action === 'add') {

        // Fetch trusted drink information.
        $st = $db->prepare(
            'SELECT menu_id, menu_name, price, stock_quantity
             FROM menu_item
             WHERE menu_id = ?
             AND availability = 1'
        );

        $st->bind_param('i', $id);
        $st->execute();

        $m = $st->get_result()->fetch_assoc();

        if ($m && (int) $m['stock_quantity'] > 0) {

            $custom = drink_customization(
                $_POST,
                $m['menu_name']
            );

            // Keep the original cart structure:
            // one entry per menu_id.

            // Adding the same drink again updates its
            // customization and increases its quantity.
            $currentQty = (int) ($_SESSION['cart'][$id] ?? 0);
            $stock = (int) $m['stock_quantity'];

            if ($currentQty >= $stock) {
                $ok = false;
                $msg = 'Maximum available stock reached.';
            } else {

                $_SESSION['cart'][$id] = min(
                    $stock,
                    $currentQty + 1
                );

                $_SESSION['custom'][$id] = $custom;

                $msg = $m['menu_name'] . ' added to your cart';
            }

        } else {

            $ok = false;
            $msg = 'This drink is unavailable.';
        }


    // REMOVE DRINK FROM CART

    } elseif ($action === 'remove') {

        unset(
            $_SESSION['cart'][$id],
            $_SESSION['custom'][$id]
        );

        $msg = 'Drink removed from cart.';


    // UPDATE DRINK QUANTITY

    } elseif ($action === 'update') {

        $qty = max(
            0,
            min(99, (int) ($_POST['quantity'] ?? 0))
        );

        $st = $db->prepare(
            'SELECT stock_quantity
             FROM menu_item
             WHERE menu_id = ?'
        );

        $st->bind_param('i', $id);
        $st->execute();

        $stock = $st->get_result()->fetch_assoc();

        $qty = min(
            $qty,
            (int) ($stock['stock_quantity'] ?? 0)
        );

        if ($qty > 0) {

            $_SESSION['cart'][$id] = $qty;

        } else {

            unset(
                $_SESSION['cart'][$id],
                $_SESSION['custom'][$id]
            );
        }

        $msg = 'Cart updated.';

    } else {

        $ok = false;
        $msg = 'Invalid cart action.';
    }

    // HANDLE AJAX RESPONSE

    if (($_POST['ajax'] ?? '') === '1') {

        header('Content-Type: application/json');

        echo json_encode([
            'ok' => $ok,
            'message' => $msg,
            'count' => cart_count()
        ]);

        exit;
    }

    // FLASH MESSAGE AND REDIRECT

    flash($msg);

    go($action === 'add' ? 'menu.php' : 'cart.php');
}



// 4. FETCH CART ITEMS AND CALCULATE TOTAL

$rows = [];
$total = 0;

foreach ($_SESSION['cart'] as $id => $qty) {

    $id = (int) $id;
    $qty = (int) $qty;

    if ($qty <= 0) {
        continue;
    }

    $st = $db->prepare(
        'SELECT *
         FROM menu_item
         WHERE menu_id = ?'
    );

    $st->bind_param('i', $id);
    $st->execute();

    $m = $st->get_result()->fetch_assoc();

    if (!$m) {
        continue;
    }

    // Get saved customization.
    $custom = $_SESSION['custom'][$id] ?? [];

    // Make sure hot drinks never show ice.
    if (is_hot_drink($m['menu_name'])) {
        $custom['extra'] = 'None';
        $_SESSION['custom'][$id]['extra'] = 'None';
    }

    // Calculate the customized unit price.
    $unitPrice = drink_unit_price(
        $m['price'],
        $custom
    );

    // Calculate item subtotal.
    $m['quantity'] = $qty;
    $m['unit_price'] = $unitPrice;
    $m['subtotal'] = round(
        $qty * $unitPrice,
        2
    );

    // Store customization with row.
    $m['customization'] = $custom;

    $total += $m['subtotal'];

    $rows[] = $m;
}

$total = round($total, 2);


// 5. INCLUDE HEADER

include 'includes/header.php';

?>


<!-- PAGE HEADER -->

<div class="pagehead">

    <div class="wrap">

        <h1>
            Your Cart
        </h1>

    </div>

</div>


<!-- SHOPPING CART SECTION -->

<section class="section wrap">


    <!-- EMPTY CART MESSAGE -->

    <?php if (!$rows): ?>

        <div class="panel center">

            <h2>
                Your cart is empty
            </h2>

            <a class="btn dark" href="menu.php">
                Browse menu
            </a>

        </div>


    <!-- CART WITH ITEMS -->

    <?php else: ?>

        <div class="cart-layout">


            <!-- CART ITEMS -->

            <div class="cart-items">

                <?php foreach ($rows as $m): ?>

                    <?php
                        $c = $m['customization'];
                    ?>

                    <article class="cart-line">


                        <!-- DRINK IMAGE -->
                        <img
                            src="<?= h(item_picture($m)) ?>"
                            alt="<?= h($m['menu_name']) ?>"
                        >


                        <!-- DRINK INFORMATION -->
                        <div class="cart-info">

                            <h3>
                                <?= h($m['menu_name']) ?>
                            </h3>


                            <!-- DRINK CUSTOMIZATION -->
                            <p>
                                <?= h(
                                    implode(
                                        ' · ',
                                        array_filter([
                                            $c['size'] ?? 'Medium',
                                            $c['milk'] ?? null,

                                            isset($c['shots'])
                                                ? $c['shots'] . ' shots'
                                                : null,

                                            isset($c['pumps'])
                                                ? $c['pumps'] . ' pumps'
                                                : null,

                                            ($c['extra'] ?? 'None') !== 'None'
                                                ? $c['extra']
                                                : null,

                                            ($c['topping'] ?? 'None') !== 'None'
                                                ? $c['topping']
                                                : null
                                        ], function ($value) {
                                            return $value !== null
                                                && $value !== '';
                                        })
                                    )
                                ) ?>
                            </p>


                            <!-- UNIT PRICE -->
                            <small>
                                <?= money($m['unit_price']) ?> each
                            </small>


                            <!-- UPDATE QUANTITY -->

                            <form class="qty-form" method="post">

                                <input
                                    type="hidden"
                                    name="csrf"
                                    value="<?= csrf() ?>"
                                >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="update"
                                >

                                <input
                                    type="hidden"
                                    name="menu_id"
                                    value="<?= (int) $m['menu_id'] ?>"
                                >


                                <!-- Decrease Quantity -->
                                <button
                                    type="button"
                                    class="qty-change"
                                    data-step="-1"
                                >
                                    −
                                </button>


                                <!-- Quantity Input -->
                                <input
                                    type="number"
                                    name="quantity"
                                    min="0"
                                    max="99"
                                    value="<?= (int) $m['quantity'] ?>"
                                    aria-label="Quantity"
                                >


                                <!-- Increase Quantity -->
                                <button
                                    type="button"
                                    class="qty-change"
                                    data-step="1"
                                >
                                    +
                                </button>


                                <!-- Update Button -->
                                <button class="qty-save" type="submit">
                                    Update
                                </button>

                            </form>

                        </div>


                        <!-- SUBTOTAL AND REMOVE -->

                        <div class="cart-line-end">

                            <!-- Item Subtotal -->
                            <b>
                                <?= money($m['subtotal']) ?>
                            </b>


                            <!-- Remove Item Form -->
                            <form method="post">

                                <input
                                    type="hidden"
                                    name="csrf"
                                    value="<?= csrf() ?>"
                                >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="remove"
                                >

                                <input
                                    type="hidden"
                                    name="menu_id"
                                    value="<?= (int) $m['menu_id'] ?>"
                                >

                                <button
                                    class="text-button"
                                    type="submit"
                                >
                                    Remove
                                </button>

                            </form>

                        </div>

                    </article>

                <?php endforeach; ?>


                <!-- CONTINUE SHOPPING -->
                <a
                    href="menu.php"
                    class="continue-shopping"
                >
                    ← Continue choosing drinks
                </a>

            </div>


            <!-- PICKUP ORDER SUMMARY -->

            <aside class="pickup-summary">

                <h2>
                    Pickup Order
                </h2>


                <!-- PICKUP TIME -->

                <label for="pickup-time">
                    Pickup time
                </label>

                <select id="pickup-time">

                    <option>As soon as possible</option>
                    <option>In 15 minutes</option>
                    <option>In 30 minutes</option>
                    <option>In 1 hour</option>

                </select>


                <!-- ORDER SUBTOTAL -->

                <div class="summary-row">

                    <span>
                        Subtotal
                    </span>

                    <span>
                        <?= money($total) ?>
                    </span>

                </div>


                <!-- ORDER TOTAL -->

                <div class="summary-row total">

                    <strong>
                        Total
                    </strong>

                    <strong>
                        <?= money($total) ?>
                    </strong>

                </div>


                
                <!-- CHECKOUT OPTIONS -->

                <div class="checkout-options">

                    <?php if (isset($_SESSION['customer_id'])): ?>

                        <!-- Logged-in Customer -->
                        <a class="btn checkout-btn"
                        href="checkout.php">
                            Place Pickup Order →
                        </a>

                    <?php else: ?>

                        <!-- Customer Login -->
                        <a class="btn login-btn"
                        href="login.php?redirect=checkout.php">
                            Login to Continue →
                        </a>

                    <?php endif; ?>

                </div>



            </aside>

        </div>

    <?php endif; ?>

</section>


<!-- =========================================
     10. FOOTER
========================================= -->

<?php include 'includes/footer.php'; ?>

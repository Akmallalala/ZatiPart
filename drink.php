
<?php

require 'config/app.php';

// Get the selected drink ID
$id = (int) ($_GET['id'] ?? 0);

// Fetch drink details from the database
$st = $db->prepare(
    'SELECT m.*, c.category_name
     FROM menu_item m
     JOIN category c ON c.category_id = m.category_id
     WHERE m.menu_id = ?
     AND m.availability = 1'
);

$st->bind_param('i', $id);
$st->execute();

$m = $st->get_result()->fetch_assoc();

// Display an error if the drink is not found
if (!$m) {
    http_response_code(404);
    exit('Drink not found.');
}

// Set page title
$title = $m['menu_name'];

// Detect hot drinks based on their names
// Example: Hot Latte, Hot Mocha, Hot Flat White
$isHotDrink = stripos(trim($m['menu_name']), 'Hot ') === 0;

include 'includes/header.php';

?>


<!-- 1. BREADCRUMB NAVIGATION -->

<div class="crumb-strip">

    <div class="wrap">

        <a href="menu.php">
            Menu
        </a>

        /

        <a href="menu.php?category=<?= $m['category_id'] ?>">
            <?= h($m['category_name']) ?>
        </a>

        /

        <b>
            <?= h($m['menu_name']) ?>
        </b>

    </div>

</div>


<!-- 2. PRODUCT SHOWCASE SECTION -->

<section class="product-showcase">

    <div class="showcase-shell">


        <!-- PRODUCT IMAGE -->

        <div class="showcase-visual">

            <div class="showcase-halo"></div>

            <img
                src="<?= h(item_picture($m)) ?>"
                alt="<?= h($m['menu_name']) ?>"
            >

            <span class="showcase-caption">
                CRAFTED FOR YOUR MOMENT · BREWSIP
            </span>

        </div>


        <!-- PRODUCT INFORMATION -->

        <div class="showcase-info">

            <span class="eyebrow">
                YOUR PERFECT CUP
            </span>

            <h1>
                <?= h($m['menu_name']) ?>
            </h1>

            <p class="showcase-desc">
                <?= h($m['description']) ?>
            </p>

            <strong class="showcase-price" id="drink-price">
                <?= money($m['price']) ?>
            </strong>


            <!-- 3. CUSTOMIZATION FORM -->

            <form
                class="showcase-form"
                method="post"
                action="cart.php"
                data-add-cart
            >

                <!-- Hidden Form Fields -->

                <input
                    type="hidden"
                    name="csrf"
                    value="<?= csrf() ?>"
                >

                <input
                    type="hidden"
                    name="action"
                    value="add"
                >

                <input
                    type="hidden"
                    name="menu_id"
                    value="<?= $id ?>"
                >

                <input
                    type="hidden"
                    name="return_to"
                    value="drink.php?id=<?= $id ?>"
                >


                <!-- DRINK SIZE -->

                <h2>
                    Make it yours
                </h2>

                <div class="showcase-sizes">

                    <!-- Small -->
                    <label>

                        <input
                            type="radio"
                            name="size"
                            value="Small"
                        >

                        <span>
                            Small
                            <small>12 fl oz</small>
                        </span>

                    </label>


                    <!-- Medium -->
                    <label>

                        <input
                            type="radio"
                            name="size"
                            value="Medium"
                            checked
                        >

                        <span>
                            Medium
                            <small>16 fl oz</small>
                        </span>

                    </label>


                    <!-- Large -->
                    <label>

                        <input
                            type="radio"
                            name="size"
                            value="Large"
                        >

                        <span>
                            Large
                            <small>24 fl oz</small>
                        </span>

                    </label>

                </div>


                <!-- DRINK CUSTOMIZATION -->

                <div class="showcase-fields">


                    <!-- Milk Selection -->

                    <label>
                        Milk

                        <select name="milk" id="milk">
                            <option value="2% Milk">2% Milk</option>
                            <option value="Whole Milk">Whole Milk</option>
                            <option value="No milk">No Milk</option>
                            <option value="Oat Milk">Oat Milk (+0.80)</option>
                            <option value="Almond Milk">Almond Milk (+0.80)</option>
                            <option value="Soy Milk">Soy Milk (+0.80)</option>
                        </select>

                    </label>


                    <!-- ADD-INS SELECTION -->
                    <!-- Only for non-hot drinks -->

                    <?php if (!$isHotDrink): ?>

                        <label>
                            Add-ins

                            <select name="extra">
                                <option value="Ice">Ice</option>
                                <option value="Extra ice">Extra ice</option>
                                <option value="No ice">No ice</option>
                            </select>

                        </label>

                    <?php endif; ?>


                    <!-- Espresso Shots -->

                    <label>
                        Espresso shots

                        <input
                            name="shots"
                            type="number"
                            min="0"
                            max="6"
                            value="0"
                        >

                    </label>


                    <!-- Syrup Pumps -->

                    <?php if (!$isHotDrink): ?>

                        <label>
                            Syrup pumps

                            <input
                                name="pumps"
                                type="number"
                                min="0"
                                max="8"
                                value="1"
                            >

                        </label>

                    <?php endif; ?>

                </div>


                <!-- ADDITIONAL CUSTOMIZATION -->

                <details class="showcase-more">

                    <summary>
                        ✦ More ways to customize
                    </summary>


                    <!-- Topping Selection -->

                    <label>
                        Topping

                        <select name="topping">
                            <option value="None">None</option>
                            <option value="Caramel drizzle">Caramel drizzle</option>
                            <option value="Chocolate sauce">Chocolate sauce</option>
                            <option value="Whipped cream">Whipped cream</option>
                        </select>

                    </label>


                    <!-- Note for Barista -->

                    <label>
                        Note for the barista

                        <input
                            name="note"
                            maxlength="80"
                            placeholder="e.g. less sweet"
                        >

                    </label>

                </details>


                <!-- ADD TO CART BUTTON -->

                <button
                    class="btn dark showcase-add"
                    type="submit"
                    id="add-to-cart-btn"
                    <?= $m['stock_quantity'] < 1 ? 'disabled' : '' ?>
                >
                    <?= $m['stock_quantity'] < 1
                        ? 'Sold out'
                        : 'Add to cart · ' . money($m['price']) ?>
                </button>


                <!-- Customization Information -->

                <p class="showcase-note">
                    Price displayed includes selected size and milk
                    adjustments. Final charges are determined at checkout.
                </p>

            </form>

        </div>

    </div>

</section>


<!-- 4. DYNAMIC PRICE CALCULATION -->

<script>

document.addEventListener("DOMContentLoaded", function () {

    // Get original drink price from database
    const basePrice = <?= json_encode((float) $m['price']) ?>;

    // Get elements
    const form = document.querySelector(".showcase-form");
    const sizeOptions = form.querySelectorAll('input[name="size"]');
    const milkSelect = document.getElementById("milk");
    const drinkPrice = document.getElementById("drink-price");
    const cartButton = document.getElementById("add-to-cart-btn");


    // Calculate price based on customization
    function updatePrice() {

        let totalPrice = basePrice;


        // 1. SIZE PRICE ADJUSTMENT

        const selectedSize = form.querySelector(
            'input[name="size"]:checked'
        )?.value || "Medium";

        if (selectedSize === "Small") {
            totalPrice -= 0.50;
        }
        else if (selectedSize === "Large") {
            totalPrice += 0.50;
        }


        // 2. MILK PRICE ADJUSTMENT

        const selectedMilk = milkSelect.value;

        if (
            selectedMilk === "Oat Milk" ||
            selectedMilk === "Almond Milk" ||
            selectedMilk === "Soy Milk"
        ) {
            totalPrice += 0.80;
        }


        // 3. UPDATE DISPLAYED PRICE

        const formattedPrice = "BND " + totalPrice.toFixed(2);

        // Update price below description
        drinkPrice.textContent = formattedPrice;

        // Update Add to Cart button
        if (!cartButton.disabled) {
            cartButton.textContent =
                "Add to cart · " + formattedPrice;
        }

    }


    // 4. EVENT LISTENERS

    // Update when size changes
    sizeOptions.forEach(function (option) {
        option.addEventListener("change", updatePrice);
    });

    // Update when milk changes
    milkSelect.addEventListener("change", updatePrice);

    // Calculate initial price
    updatePrice();

});

</script>


<!-- 5. FOOTER -->

<?php include 'includes/footer.php'; ?>

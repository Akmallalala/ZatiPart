<?php

// 1. INCLUDE ADMIN CONFIGURATION

require 'common.php';

$err = '';


// 2. HANDLE FORM SUBMISSION

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $id = (int) ($_POST['id'] ?? 0);

    try {

        // 2.1 DELETE MENU ITEM

        if (($_POST['action'] ?? '') === 'delete') {

            $st = $db->prepare(
                'DELETE FROM menu_item WHERE menu_id=?'
            );

            $st->bind_param('i', $id);
            $st->execute();

        } else {

            // 2.2 GET PRODUCT DETAILS

            $cat = (int) ($_POST['category_id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $desc = trim($_POST['description'] ?? '');
            $price = (float) ($_POST['price'] ?? 0);
            $stock = (int) ($_POST['stock'] ?? 0);
            $avail = isset($_POST['availability']) ? 1 : 0;
            $image = basename($_POST['image'] ?? '');


            // 2.3 VALIDATE PRODUCT DETAILS

            if (
                !$cat ||
                $name === '' ||
                $price < 0 ||
                $stock < 0 ||
                strlen($name) > 100 ||
                strlen($image) > 255
            ) {

                throw new Exception(
                    'Please enter valid product details.'
                );

            }


            // 2.4 UPDATE EXISTING MENU ITEM

            if ($id) {

                $st = $db->prepare(
                    'UPDATE menu_item
                     SET category_id=?,
                         menu_name=?,
                         description=?,
                         price=?,
                         stock_quantity=?,
                         availability=?,
                         image=?
                     WHERE menu_id=?'
                );

                $st->bind_param(
                    'issdiisi',
                    $cat,
                    $name,
                    $desc,
                    $price,
                    $stock,
                    $avail,
                    $image,
                    $id
                );

            } else {

                // 2.5 ADD NEW MENU ITEM

                $st = $db->prepare(
                    'INSERT INTO menu_item
                     (category_id, menu_name, description,
                      price, stock_quantity, availability, image)
                     VALUES (?, ?, ?, ?, ?, ?, ?)'
                );

                $st->bind_param(
                    'issdiis',
                    $cat,
                    $name,
                    $desc,
                    $price,
                    $stock,
                    $avail,
                    $image
                );

            }


            // 2.6 EXECUTE DATABASE QUERY

            $st->execute();

        }


        // 2.7 DISPLAY SUCCESS MESSAGE

        flash('Menu updated.');
        go('items.php');

    } catch (Throwable $e) {

        $err = 'Could not save/delete item. It may be referenced by an order or review, or the details may be invalid.';

    }

}


// 3. FETCH MENU ITEM FOR EDITING

$edit = null;

if (isset($_GET['edit'])) {

    $id = (int) $_GET['edit'];

    $st = $db->prepare(
        'SELECT * FROM menu_item WHERE menu_id=?'
    );

    $st->bind_param('i', $id);
    $st->execute();

    $edit = $st->get_result()->fetch_assoc();

}


// 4. FETCH PRODUCT CATEGORIES

$cats = $db->query(
    'SELECT * FROM category ORDER BY category_name'
)->fetch_all(MYSQLI_ASSOC);


// 5. FETCH ALL MENU ITEMS

$items = $db->query(
    'SELECT m.*, c.category_name
     FROM menu_item m
     JOIN category c ON c.category_id = m.category_id
     ORDER BY m.menu_id DESC'
);


// 6. FETCH AVAILABLE PRODUCT IMAGES

$images = array_map(
    'basename',
    glob('../assets/img/*.png')
);


// 7. DISPLAY ADMIN HEADER

admin_head('Manage Menu');

?>


<!-- 8. MENU MANAGEMENT SECTION -->

<section class="section wrap">

    <h1>Menu Management (CRUD)</h1>


    <!-- 8.1 DISPLAY ERROR MESSAGE -->

    <?php if ($err): ?>

        <p class="error">
            <?= h($err) ?>
        </p>

    <?php endif; ?>


    <!-- 9. ADD OR EDIT MENU ITEM FORM -->

    <form method="post" class="panel">

        <h2>
            <?= $edit ? 'Edit menu item' : 'Add menu item' ?>
        </h2>


        <!-- 9.1 HIDDEN FORM FIELDS -->

        <input
            type="hidden"
            name="csrf"
            value="<?= csrf() ?>"
        >

        <input
            type="hidden"
            name="id"
            value="<?= $edit['menu_id'] ?? 0 ?>"
        >


        <!-- 9.2 PRODUCT DETAILS -->

        <div class="two">

            <div>

                <!-- Product Name -->

                <label>Product name</label>

                <input
                    name="name"
                    required
                    maxlength="100"
                    value="<?= h($edit['menu_name'] ?? '') ?>"
                >


                <!-- Product Category -->

                <label>Category</label>

                <select name="category_id" required>

                    <?php foreach ($cats as $c): ?>

                        <option
                            value="<?= $c['category_id'] ?>"
                            <?= ($edit['category_id'] ?? 0) == $c['category_id'] ? 'selected' : '' ?>
                        >
                            <?= h($c['category_name']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>


                <!-- Product Description -->

                <label>Description</label>

                <textarea
                    name="description"
                    rows="3"
                ><?= h($edit['description'] ?? '') ?></textarea>

            </div>


            <!-- 9.3 PRODUCT PRICE AND AVAILABILITY -->

            <div>

                <!-- Product Price -->

                <label>Price (BND)</label>

                <input
                    type="number"
                    name="price"
                    min="0"
                    step="0.01"
                    required
                    value="<?= h($edit['price'] ?? '5.90') ?>"
                >


                <!-- Stock Quantity -->

                <label>Stock quantity</label>

                <input
                    type="number"
                    name="stock"
                    min="0"
                    required
                    value="<?= h($edit['stock_quantity'] ?? '20') ?>"
                >


                <!-- Product Image -->

                <label>Image from supplied archive</label>

                <select name="image">

                    <?php foreach ($images as $im): ?>

                        <option
                            value="<?= h($im) ?>"
                            <?= ($edit['image'] ?? '') === $im ? 'selected' : '' ?>
                        >
                            <?= h($im) ?>
                        </option>

                    <?php endforeach; ?>

                </select>


                <!-- Product Availability -->

                <label>

                    <input
                        type="checkbox"
                        name="availability"
                        <?= !$edit || $edit['availability'] ? 'checked' : '' ?>
                    >

                    Available for ordering

                </label>

            </div>

        </div>


        <!-- 9.4 FORM ACTION BUTTONS -->

        <p>

            <button class="btn dark">
                Save item
            </button>

            <a class="btn" href="items.php">
                Clear form
            </a>

        </p>

    </form>


    <!-- 10. ALL MENU ITEMS -->

    <h2>All menu items</h2>

    <div class="table-scroll">

        <table>

            <tr>
                <th>Photo</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Available</th>
                <th>Actions</th>
            </tr>


            <!-- 10.1 DISPLAY MENU ITEMS -->

            <?php foreach ($items as $m): ?>

                <tr>

                    <!-- Product Image -->

                    <td>

                        <img
                            src="../<?= h(image_path($m['image'])) ?>"
                            alt=""
                        >

                    </td>


                    <!-- Product Name -->

                    <td>
                        <?= h($m['menu_name']) ?>
                    </td>


                    <!-- Product Category -->

                    <td>
                        <?= h($m['category_name']) ?>
                    </td>


                    <!-- Product Price -->

                    <td>
                        <?= money($m['price']) ?>
                    </td>


                    <!-- Stock Quantity -->

                    <td>
                        <?= $m['stock_quantity'] ?>
                    </td>


                    <!-- Availability -->

                    <td>
                        <?= $m['availability'] ? 'Yes' : 'No' ?>
                    </td>


                    <!-- 10.2 PRODUCT ACTION BUTTONS -->

                    <td>

                        <div class="actions">

                            <!-- Edit Product -->

                            <a
                                class="btn"
                                href="items.php?edit=<?= $m['menu_id'] ?>"
                            >
                                Edit
                            </a>


                            <!-- Delete Product -->

                            <form
                                method="post"
                                onsubmit="return confirm('Delete this product? Existing order records may prevent deletion.')"
                            >

                                <input
                                    type="hidden"
                                    name="csrf"
                                    value="<?= csrf() ?>"
                                >

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= $m['menu_id'] ?>"
                                >

                                <button
                                    class="btn"
                                    name="action"
                                    value="delete"
                                >
                                    Delete
                                </button>

                            </form>

                        </div>

                    </td>

                </tr>

            <?php endforeach; ?>

        </table>

    </div>

</section>


<?php

// 11. DISPLAY ADMIN FOOTER

admin_foot();

?>

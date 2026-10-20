<?php

// 1. INCLUDE ADMIN CONFIGURATION

require 'common.php';

$err = '';


// 2. HANDLE FORM SUBMISSION

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');

    try {

        // 2.1 DELETE CATEGORY

        if (($_POST['action'] ?? '') === 'delete') {

            $st = $db->prepare(
                'DELETE FROM category WHERE category_id=?'
            );

            $st->bind_param('i', $id);
            $st->execute();

        }

        // 2.2 VALIDATE CATEGORY NAME

        elseif ($name === '') {

            $err = 'Category name is required.';

        }

        // 2.3 UPDATE EXISTING CATEGORY

        elseif ($id) {

            $st = $db->prepare(
                'UPDATE category SET category_name=? WHERE category_id=?'
            );

            $st->bind_param('si', $name, $id);
            $st->execute();

        }

        // 2.4 ADD NEW CATEGORY

        else {

            $st = $db->prepare(
                'INSERT INTO category(category_name) VALUES(?)'
            );

            $st->bind_param('s', $name);
            $st->execute();

        }


        // 2.5 DISPLAY SUCCESS MESSAGE

        if (!$err) {

            flash('Category saved.');
            go('categories.php');

        }

    } catch (mysqli_sql_exception $e) {

        $err = 'Cannot delete this category while menu items reference it.';

    }

}


// 3. FETCH ALL CATEGORIES

$cats = $db->query(
    'SELECT * FROM category ORDER BY category_id'
);


// 4. DISPLAY ADMIN HEADER

admin_head('Categories');

?>


<!-- 5. CATEGORY MANAGEMENT SECTION -->

<section class="section wrap">

    <h1>Manage Categories</h1>


    <!-- 6. DISPLAY ERROR MESSAGE -->

    <?php if ($err): ?>

        <p class="error">
            <?= h($err) ?>
        </p>

    <?php endif; ?>


    <!-- 7. ADD NEW CATEGORY FORM -->

    <form method="post" class="panel">

        <input
            type="hidden"
            name="csrf"
            value="<?= csrf() ?>"
        >

        <label>New category</label>

        <input
            name="name"
            maxlength="100"
            required
        >

        <p>
            <button class="btn dark">
                Add category
            </button>
        </p>

    </form>


    <!-- 8. CATEGORY LIST TABLE -->

    <div class="table-scroll">

        <table>

            <tr>
                <th>ID</th>
                <th>Name (edit inline)</th>
                <th>Actions</th>
            </tr>


            <!-- 8.1 DISPLAY EACH CATEGORY -->

            <?php foreach ($cats as $c): ?>

                <tr>

                    <!-- Category ID -->

                    <td>
                        <?= $c['category_id'] ?>
                    </td>


                    <!-- 8.2 EDIT CATEGORY FORM -->

                    <td>

                        <form
                            method="post"
                            id="c<?= $c['category_id'] ?>"
                        >

                            <input
                                type="hidden"
                                name="csrf"
                                value="<?= csrf() ?>"
                            >

                            <input
                                type="hidden"
                                name="id"
                                value="<?= $c['category_id'] ?>"
                            >

                            <input
                                name="name"
                                value="<?= h($c['category_name']) ?>"
                                required
                            >

                        </form>

                    </td>


                    <!-- 8.3 CATEGORY ACTION BUTTONS -->

                    <td>

                        <!-- Save Changes -->

                        <button
                            class="btn"
                            form="c<?= $c['category_id'] ?>"
                        >
                            Save
                        </button>


                        <!-- Delete Category -->

                        <form
                            method="post"
                            style="display:inline"
                            onsubmit="return confirm('Delete category?')"
                        >

                            <input
                                type="hidden"
                                name="csrf"
                                value="<?= csrf() ?>"
                            >

                            <input
                                type="hidden"
                                name="id"
                                value="<?= $c['category_id'] ?>"
                            >

                            <button
                                class="btn"
                                name="action"
                                value="delete"
                            >
                                Delete
                            </button>

                        </form>

                    </td>

                </tr>

            <?php endforeach; ?>

        </table>

    </div>

</section>


<?php

// 9. DISPLAY ADMIN FOOTER

admin_foot();

?>

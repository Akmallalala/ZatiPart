<?php

require 'config/app.php';

// Set page title
$title = 'Products';

// Get selected category and tab
$cat = max(0, (int) ($_GET['category'] ?? 0));
$tab = $_GET['tab'] ?? 'menu';

// Validate selected tab
if (!in_array($tab, ['menu', 'trending'], true)) {
    $tab = 'menu';
}


// 1. FETCH PRODUCT CATEGORIES

$cats = $db->query(
    'SELECT *
     FROM category
     ORDER BY category_id'
)->fetch_all(MYSQLI_ASSOC);


// 2. FETCH AVAILABLE MENU ITEMS

$q = 'SELECT m.*, c.category_name
      FROM menu_item m
      JOIN category c ON c.category_id = m.category_id
      WHERE m.availability = 1';

// Filter products by selected category
if ($cat && $tab === 'menu') {

    $st = $db->prepare(
        $q . ' AND m.category_id = ?
               ORDER BY m.menu_name'
    );

    $st->bind_param('i', $cat);
    $st->execute();

    $items = $st->get_result()->fetch_all(MYSQLI_ASSOC);

} else {

    $items = $db->query(
        $q . ' ORDER BY m.menu_id'
    )->fetch_all(MYSQLI_ASSOC);

}


// 3. FILTER FEATURED AND FAVOURITE DRINKS

if ($tab === 'trending') {

    $trendingDrinks = [
        'Cookies & Cream Frappe',
        'Spanish Latte',
        'Vanilla Latte',
        'Dirty Matcha Latte',
        'Swedish Berry'
    ];

    // Filter only selected trending drinks
    $items = array_filter($items, function ($m) use ($trendingDrinks) {

        return in_array(
            $m['menu_name'],
            $trendingDrinks,
            true
        );

    });

    // Sort drinks based on the order above
    usort($items, function ($a, $b) use ($trendingDrinks) {

        $positionA = array_search(
            $a['menu_name'],
            $trendingDrinks,
            true
        );

        $positionB = array_search(
            $b['menu_name'],
            $trendingDrinks,
            true
        );

        return $positionA <=> $positionB;

    });

}


// 4. DETERMINE SELECTED CATEGORY

$selected = 'All Drinks';

foreach ($cats as $c) {

    if ($cat == (int) $c['category_id']) {
        $selected = $c['category_name'];
    }

}


// 5. INCLUDE HEADER

include 'includes/header.php';

?>


<!-- 6. PRODUCT NAVIGATION TABS -->

<div class="product-tabs">

    <div class="wrap">

        <?php foreach (
            [
                'menu' => 'Menu',
                'trending' => 'Trending'
            ] as $k => $v
        ): ?>

            <a
                class="<?= $tab === $k ? 'on' : '' ?>"
                href="menu.php?tab=<?= $k ?>"
            >
                <?= h($v) ?>
            </a>

        <?php endforeach; ?>

    </div>

</div>


<!-- 7. MENU PAGE LAYOUT -->

<div class="wrap menu-layout">


    <!-- MENU SIDEBAR -->

    <aside class="menu-sidebar">


        <!-- THE LATEST -->

        <h3>
            The Latest
        </h3>

        <a href="menu.php?tab=trending">
            Seasonal Picks
        </a>


        <!-- COFFEE AND ESPRESSO -->

        <h3>
            Coffee & Espresso
        </h3>

        <?php foreach ($cats as $c): ?>

            <?php if (
                stripos($c['category_name'], 'coffee') !== false
            ): ?>

                <a
                    class="<?= $cat == (int) $c['category_id'] && $tab === 'menu' ? 'on' : '' ?>"
                    href="menu.php?category=<?= $c['category_id'] ?>"
                >
                    <?= h($c['category_name']) ?>
                </a>

            <?php endif; ?>

        <?php endforeach; ?>


        <!-- BLENDED DRINKS -->

        <h3>
            Blended
        </h3>

        <?php foreach ($cats as $c): ?>

            <?php if (
                stripos($c['category_name'], 'frappe') !== false
            ): ?>

                <a
                    class="<?= $cat == (int) $c['category_id'] ? 'on' : '' ?>"
                    href="menu.php?category=<?= $c['category_id'] ?>"
                >
                    <?= h($c['category_name']) ?>
                </a>

            <?php endif; ?>

        <?php endforeach; ?>


        <!-- TEA AND MATCHA -->

        <h3>
            Tea & Matcha
        </h3>

        <?php foreach ($cats as $c): ?>

            <?php if (
                stripos($c['category_name'], 'matcha') !== false ||
                stripos($c['category_name'], 'tea') !== false
            ): ?>

                <a
                    class="<?= $cat == (int) $c['category_id'] ? 'on' : '' ?>"
                    href="menu.php?category=<?= $c['category_id'] ?>"
                >
                    <?= h($c['category_name']) ?>
                </a>

            <?php endif; ?>

        <?php endforeach; ?>


        <!-- ALL DRINKS -->

        <a
            href="menu.php"
            class="<?= $cat === 0 && $tab === 'menu' ? 'on' : '' ?>"
        >
            All Drinks
        </a>

    </aside>


    <!-- MENU CONTENT -->

    <section class="menu-content">


        <!-- BREADCRUMB NAVIGATION -->

        <div class="crumb">

            Menu /

            <strong>
                <?= h(
                    $tab === 'menu'
                        ? $selected
                        : ucfirst($tab)
                ) ?>
            </strong>

        </div>


        <!-- PAGE TITLE -->

        <h1>
            <?= h(
                $tab === 'menu'
                    ? $selected
                    : ucfirst($tab)
            ) ?>
        </h1>


        <!-- 8. GROUP DRINKS BY CATEGORY -->

        <?php

        $groups = [];

        foreach ($items as $m) {
            $groups[$m['category_name']][] = $m;
        }

        ?>


        <!-- 9. DISPLAY DRINK CATEGORIES -->

        <?php foreach ($groups as $group => $drinks): ?>


            <!-- CATEGORY TITLE -->

            <h2>
                <?= h($group) ?>
            </h2>


            <!-- DRINK GRID -->

            <div class="drink-grid">

                <?php foreach ($drinks as $m): ?>

                    <a
                        class="drink-tile"
                        href="drink.php?id=<?= $m['menu_id'] ?>"
                    >


                        <!-- DRINK IMAGE -->

                        <span class="drink-circle">

                            <img
                                src="<?= h(item_picture($m)) ?>"
                                alt="<?= h($m['menu_name']) ?>"
                            >

                        </span>


                        <!-- DRINK NAME -->

                        <span>
                            <?= h($m['menu_name']) ?>
                        </span>


                        <!-- DRINK PRICE -->

                        <small>
                            <?= money($m['price']) ?>
                        </small>

                    </a>

                <?php endforeach; ?>

            </div>

        <?php endforeach; ?>


        <!-- 10. EMPTY MENU MESSAGE -->

        <?php if (!$items): ?>

            <p>
                No available drinks found.
            </p>

        <?php endif; ?>


    </section>

</div>


<!-- 11. FOOTER -->

<?php include 'includes/footer.php'; ?>

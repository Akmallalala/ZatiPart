<?php

// 1. INCLUDE CONFIGURATION AND VERIFY ADMIN LOGIN

require_once __DIR__ . '/../config/app.php';

require_admin();


// 2. ADMIN HEADER FUNCTION

function admin_head($title) {

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title><?= h($title) ?> | BREWSIP Admin</title>

        <!-- 2.1 ADMIN STYLESHEET -->

        <link rel="stylesheet" href="../assets/style.css">

    </head>

    <body>

        <!-- 3. ADMIN HEADER -->

        <header>

            <nav class="wrap nav">

                <!-- 3.1 ADMIN LOGO -->

                <a class="logo" href="dashboard.php">
                    BREWSIP ADMIN
                </a>

                <!-- 3.2 CUSTOMER WEBSITE LINK -->

                <a href="../index.php">
                    Customer site ↗
                </a>

            </nav>

        </header>


        <!-- 4. ADMIN NAVIGATION -->

        <div class="admin-nav">

            <div class="wrap">

                <a href="dashboard.php">
                    Dashboard
                </a>

                <a href="items.php">
                    Menu CRUD
                </a>

                <a href="categories.php">
                    Categories
                </a>

                <a href="orders.php">
                    Orders
                </a>

                <a href="report.php">
                    Sales report
                </a>

                <a href="logout.php">
                    Logout
                </a>

            </div>

        </div>


        <!-- 5. MAIN ADMIN CONTENT -->

        <main>

            <!-- 5.1 FLASH NOTIFICATION -->

            <?php if ($f = flash()): ?>

                <p class="notice wrap">
                    <?= h($f) ?>
                </p>

            <?php endif; ?>

    <?php

}


// 6. ADMIN FOOTER FUNCTION

function admin_foot() {

    ?>

        <!-- 6.1 CLOSE MAIN CONTENT -->

        </main>


        <!-- 6.2 ADMIN FOOTER -->

        <footer>

            <p class="copyright">
                BREWSIP Administration · WD1 Project
            </p>

        </footer>


    </body>

    </html>

    <?php

}

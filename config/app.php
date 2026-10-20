<?php

// 1. START SESSION AND CONNECT DATABASE

session_start();
require_once __DIR__ . '/db.php';


// 2. FORMAT TEXT SAFELY

function h($v) {
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}


// 3. FORMAT PRICE IN BND

function money($v) {
    return 'BND ' . number_format((float) $v, 2);
}


// 4. GENERATE CSRF TOKEN

function csrf() {

    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}


// 5. VERIFY CSRF TOKEN

function verify_csrf() {

    if (!hash_equals(csrf(), $_POST['csrf'] ?? '')) {

        http_response_code(403);
        exit('Invalid form token.');

    }

}


// 6. DISPLAY FLASH MESSAGES

function flash($s = null) {

    if ($s !== null) {

        $_SESSION['flash'] = $s;
        return;

    }

    $x = $_SESSION['flash'] ?? '';

    unset($_SESSION['flash']);

    return $x;
}


// 7. REDIRECT USER

function go($url) {

    header('Location: ' . $url);
    exit;

}


// 8. CHECK CUSTOMER LOGIN STATUS

function logged() {
    return isset($_SESSION['customer_id']);
}


// 9. REQUIRE CUSTOMER LOGIN

function require_user() {

    if (!logged()) {

        flash('Please sign in first.');
        go('login.php');

    }

}


// 10. REQUIRE ADMIN LOGIN

function require_admin() {

    if (empty($_SESSION['admin_id'])) {
        go('login.php');
    }

}


// 11. GET PRODUCT IMAGE PATH

function image_path($v) {

    $v = basename((string) $v);

    return $v && is_file(__DIR__ . '/../assets/img/' . $v)
        ? 'assets/img/' . rawurlencode($v)
        : 'assets/img/products-coffee-1.png';

}


// 12. COUNT SHOPPING CART ITEMS

function cart_count() {
    return array_sum($_SESSION['cart'] ?? []);
}


// 13. CALCULATE DRINK CUSTOMISATION PRICE

function customization_price($size, $milk, $extra) {

    $size_price = match ($size) {
        'Small' => -0.50,
        'Large' => 0.50,
        default => 0.00
    };

    $milk_price = in_array(
        $milk,
        ['Oat Milk', 'Almond Milk', 'Soy Milk'],
        true
    ) ? 0.80 : 0.00;

    $extra_price = $extra === 'Whipped cream' ? 1.00 : 0.00;

    return $size_price + $milk_price + $extra_price;
}


// 14. GET MENU ITEM PICTURE

function item_picture($m) {
    return image_path($m['image'] ?? '');
}

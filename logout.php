
<?php

require 'config/app.php';

// Remove customer login information
unset(
    $_SESSION['customer_id'],
    $_SESSION['customer_name']
);

// Regenerate session ID for security
session_regenerate_id(true);

// Show success notification
flash('Signed out successfully.');

// Redirect customer to homepage
go('index.php');

?>

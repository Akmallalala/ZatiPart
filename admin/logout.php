<?php

// 1. INCLUDE CONFIGURATION

require '../config/app.php';


// 2. REMOVE ADMINISTRATOR SESSION

unset($_SESSION['admin_id']);


// 3. REGENERATE SESSION ID FOR SECURITY

session_regenerate_id(true);


// 4. REDIRECT TO ADMIN LOGIN PAGE

go('login.php');

?>

<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try { 
    $db = new mysqli(
        'localhost',
        'root',
        '',
        'coffee_shop'); 
        
    $db->set_charset('utf8mb4'); 
    }
catch(Throwable $e){ http_response_code(500); 
exit('Database unavailable. Check XAMPP MySQL and config/db.php.'); 
}

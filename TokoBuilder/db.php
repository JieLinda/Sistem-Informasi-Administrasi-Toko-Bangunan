<?php
// Establishing a database connection
$con = new mysqli('localhost', 'root', '', 'toko4');

// Check connection
if ($con->connect_error) {
    die('Connection failed: ' . $con->connect_error);
}

// http://localhost/<your subfolder>/purchase/index.php
$subfolder = "tekweb-project";

function base_url() {
    global $subfolder;

    return sprintf(
        "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['HTTP_HOST'],
        $subfolder == '' ? '' : '/' . $subfolder
    );
}
?>


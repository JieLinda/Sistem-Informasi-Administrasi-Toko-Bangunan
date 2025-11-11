<?php
// Establishing a database connection
$con = new mysqli('localhost', 'root', '', 'toko4');

// Check connection
if ($con->connect_error) {
    die('Connection failed: ' . $con->connect_error);
}

?>
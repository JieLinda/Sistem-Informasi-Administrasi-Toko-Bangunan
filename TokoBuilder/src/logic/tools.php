<?php
$conn = new mysqli("localhost", "root", "", "tokobuilder");
function query($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function query_no_return($query) {
    global $conn;
    mysqli_query($conn, $query);
    
}

function login($username, $password) {
    global $conn;
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    query($query);
    return mysqli_affected_rows($conn);
}

function base_url() {
    global $subfolder;

    return sprintf(
        "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['HTTP_HOST'],
        $subfolder == '' ? '' : '/' . $subfolder
    );
}


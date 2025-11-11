<?php

include 'db2.php';

session_start();

if (isset($_POST['username']) && isset($_POST['password'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    // cek username 
    $res = mysqli_query($con, "SELECT * FROM user WHERE username = '$username'");

    if ( mysqli_num_rows($res) === 1) {

        // cek password
        $row = mysqli_fetch_assoc($res);
        if ( password_verify($password, $row["password"])){
            $_SESSION['username'] = $row['username'];
            header("Location: product.php");
            exit();
        } else {
            echo "<script>
            alert('Password Salah!')
            </script>
            ";
            header("Location: login.php");
            exit();
        }
    } else {
        echo "<script>
            alert('Username tidak ditemukan!')
            </script>
            ";
            header("Location: login.php");
            exit();
    }
} 
?>
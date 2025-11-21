<?php
    session_start();
    require '../logic/tools.php';

    if(isset($_POST['login'])){
        if(login($_POST['username'], $_POST['password']) > 0){
            $_SESSION['username'] = $_POST['username'];
            header("Location: dashboard.php");
        }
        else{
            echo '<h2>Username/Password Salah</h2>';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class='d-flex items-center justify-content-center flex-column position-absolute translate-middle top-50 start-50 gap-4'>
    <h1 class="display-4">Welcome, Please Login!</h1>
    <div class="d-grid w-2/3 rounded-md bg-purple-700 text-white p-4 shadow">
        <form action="" method="post">
            <label class="form-label" for="username">Username</label>
            <input class='form-control' type="text" name="username" id="username">
            <label class="form-label" for="password">Password</label>
            <input class="form-control" type="password" name="password" id="password"></label>
            <br>
            <button class="btn btn-light col-12" type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>
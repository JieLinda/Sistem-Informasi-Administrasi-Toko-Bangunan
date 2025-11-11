<?php 
require 'functions.php';


if (isset($_POST["register"])){

    if (registrasi($_POST) > 0){
        echo "<script>
        alert('User berhasil ditambah!')
        </script>
        ";
        header("Location: login.php");
    } else {
        echo mysqli_error($con);
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
<?php include 'head.php'; ?>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 h-screen flex flex-col">
    <!-- Full Width Header -->
    <header class="bg-black text-white py-4 px-6 flex justify-between items-center w-full">
        <h1 class="text-xl font-semibold">Information System</h1>
        <img alt="User  profile picture" class="rounded-full" height="40" src="https://storage.googleapis.com/a1aa/image/6SeyLSBlUxTLfEygcTCmONTntQMZamEDRazHWvz5G280KHzTA.jpg" width="40"/>
    </header>
    <div class="flex flex-grow items-center justify-center">
        <!-- Main Content -->
        <div class="w-full max-w-md">
            <main class="p-6">
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-2xl font-semibold text-purple-600 mb-4 text-center">Sign Up</h2>
                    <form action="" method="POST">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="username">Username</label>
                            <input class="border border-gray-300 rounded-lg px-3 py-2 w-full" type="text" id="username" name="username" required placeholder="Enter your username"/>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                            <input class="border border-gray-300 rounded-lg px-3 py-2 w-full" type="password" id="password" name="password" required placeholder="Enter your password"/>
                        </div>
                        <div class="mb-4">
                            <button type="submit" name="register" class="bg-purple-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-purple-700 w-full">Sign Up</button>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-600">Already have an account?</p>
                            <a href="login.php" class="text-purple-600 font-bold hover:underline">Login Here</a>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
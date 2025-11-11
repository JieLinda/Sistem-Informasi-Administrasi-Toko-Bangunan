<?php 
require 'authen.php';
require 'functions.php';

if ( !isset($_SESSION['username']) ){
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$id = $_SESSION["id"];

if (isset($_POST["confirm"])){

    if ( isset($_POST["nama_supplier"]) && isset($_POST["nomor_telepon"]) ) {

        if ( updatesup($_POST,$id) > 0 ){
            echo "
            <script>
            alert('Data berhasil diupdate!')
            </script>
            ";
            header("Location: supplier.php");
            exit();
        } else{
            echo "
            <script>
            alert('Data gagal diupdate!')
            </script>
            ";
            header("Location: supplier.php");
            exit();
        }
        
 

    }

}

// Close the connection
$con->close();
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
<body class="bg-gray-100">
    <div class="flex flex-col">
        <!-- Full Width Header -->
        <header class="bg-black text-white py-4 px-6 flex justify-between items-center w-full">
            <h1 class="text-xl font-semibold">
                Information System
            </h1>
            <a class="nav-link text-white" href="logout.php">Logout</a>
            <img alt="User  profile picture" class="rounded-full" height="40" src="https://storage.googleapis.com/a1aa/image/6SeyLSBlUxTLfEygcTCmONTntQMZamEDRazHWvz5G280KHzTA.jpg" width="40"/>
        </header>
        <div class="flex">
            <!-- Sidebar -->
            <div class="w-1/5 bg-white h-screen shadow-lg">
                <div class="flex flex-col items-center py-10">
                    <img alt="Admin profile picture" class="rounded-full mb-4" height="100" src="https://storage.googleapis.com/a1aa/image/1p3FXQ32tVbvH1aNReA5de5AUWaoXncsKXgGzy21qyy1KHzTA.jpg" width="100"/>
                    <h2 class="text-lg font-semibold">
                        Admin
                    </h2>
                    <span class="text-sm text-green-500 bg-green-100 px-2 py-1 rounded-full">
                        Administrator
                    </span>
                </div>
                <nav class="mt-10">
                    <ul>
                        <li class="flex items-center px-10 py-3 text-gray-600 hover:bg-gray-200">
                            <a href="product.php" class="flex items-center">
                                <i class="fas fa-box mr-3"></i>
                                <span>Product</span>
                            </a>
                        </li>
                        <li class="flex items-center px-10 py-3 text-gray-600 bg-gray-100 text-gray-600">
                            <a href="supplier.php" class="flex items-center">    
                                <i class="fas fa-truck mr-3"></i>
                                <span>Supplier</span>
                            </a>
                        </li>
                        <li class="flex items-center px-10 py-3 text-gray-600 hover:bg-gray-200">
                            <a href="sales.php" class="flex items-center">
                                <i class="fas fa-exchange-alt mr-3"></i>
                                <span>Sales</span>
                            </a>
                        </li>
                        <li class="flex items-center px-10 py-3 text-gray-600 hover:bg-gray-200">
                            <a href="Dashboard.php" class="flex items-center">
                                <i class="fas fa-shopping-cart mr-3"></i>
                                <span>Purchase</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <!-- Main Content -->
            <div class="w-4/5">
                <main class="p-6">
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h2 class="text-2xl font-semibold text-purple-600 mb-4">
                            Tambah Supplier
                        </h2>
                        <form action="" method="POST">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="nama_supplier">
                                    Nama Supplier
                                </label>
                                <input class="border border-gray-300 rounded-lg px-3 py-2 w-full" type="text" id="nama_supplier" name="nama_supplier" required />
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="nomor_telepon">
                                    Nomor Telepon
                                </label>
                                <input class="border border-gray-300 rounded-lg px-3 py-2 w-full" type="tel" id="nomor_telepon" name="nomor_telepon" required />
                            </div>
                            <div class="flex justify-between">
                                <button type="button" class=" bg-gray-400 text-white px-4 py-2 rounded-lg" onclick="window.location.href='supplier.php'">
                                    Cancel
                                </button>
                                <button type="submit" name="confirm" class="bg-purple-600 text-white px-4 py-2 rounded-lg">
                                    Confirm
                                </button>
                            </div>
                        </form>
                    </div>
                </main>
            </div>
        </div>
    </div>
</body>
</html>
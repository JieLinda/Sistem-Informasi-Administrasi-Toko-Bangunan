<?php
require '../logic/vendor/autoload.php';
session_start();
if(!isset($_SESSION['username'])){
    header("Location: index.php");
}

use Dompdf\Dompdf;
use Dompdf\Options;

$dompdf = new Dompdf();
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
    <script src='tailwind.config.js'></script>

</head>
<body class="bg-sky-100 font-sans">

<header class="bg-purple-700 text-white py-3 px-6">
    <div class="container mx-auto flex items-center justify-between">
    <a href="headerManager/dashboard.php" class="hover:underline font-bold">Sistem Informasi</a>
      <nav class="flex space-x-6">
      <a href="headerManager/generate.php" class="hover:underline">Flyer/Catalog</a>
        <a href="headerManager/restock.php" class="hover:underline">Restock</a>
        <a href="headerManager/customers.php" class="hover:underline">Customers</a>
        <a href="headerManager/sales.php" class="hover:underline">Sales</a>
        <a href="headerManager/products.php" class="hover:underline">Products</a>
        <a href="headerManager/suppliers.php" class="hover:underline">Suppliers</a>
        <a class="hover:underline dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Finance
            <span class="caret"></span></a>
        <ul class="dropdown-menu">
            <li class="dropdown-item"><a href="headerManager/hutang.php">Hutang</a></li>
            <li class="dropdown-item"><a href="headerManager/piutang.php">Piutang</a></li>
            <li class="dropdown-item"><a href="headerManager/pendapatan.php">Pendapatan</a></li>
        </ul>
        <a href="../logout.php" class="hover:underline">Logout</a>
      </nav>
    </div>
  </header>

    <div class="flex justify-center items-start py-32 bg-sky-100">
        <div class="flex space-x-6">
            <div>
            <form method="post" action="generateFlyer.php">
                    <button type="submit" class="bg-violet-800 text-white w-[150px] text-2xl px-8 py-3 rounded-lg transition-transform transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-sky-500">
                        Flyer
                    </button>
                </form>
            </div>

            <div>
                <form method="post" action="katalog.php">
                    <button type="submit" class="bg-sky-400 text-white text-2xl w-[150px] px-8 py-3 rounded-lg transition-transform transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-sky-500">
                        Catalog
                    </button>
                </form>
            </div>

        </div>

    </div>

</body>
</html>

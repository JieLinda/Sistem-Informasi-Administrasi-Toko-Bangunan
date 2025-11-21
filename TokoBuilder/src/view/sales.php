<?php 
require '../logic/watchdog.php';
session_start();
if(!isset($_SESSION['username'])){
    header("Location: index.php");
}

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $updates = json_decode($_POST['updates'], true);

    foreach ($updates as $product_id => $quantity) {
        $conn->query("UPDATE produk SET stock = stock - $quantity WHERE product_id = $product_id");

        $result = $conn->query("SELECT product_id FROM produk WHERE product_id = $product_id");
        $product = $result->fetch_assoc();
        $harga = $conn->query("SELECT product_price FROM produk WHERE product_id = $product_id")->fetch_assoc()['product_price'];

        $produk_id = $product['product_id'];
        $total = $harga * $quantity;

        $conn->query("INSERT INTO jurnal (product_id, amount_sold, total) VALUES ($produk_id, $quantity, $total)");
    }
    $_POST = [];
    exit;
}

$sql = 'SELECT * FROM produk';
$result = $conn->query($sql);
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        let productCounts = {}; // Tracks the count for each product
        const products = <?= json_encode($products); ?>; // Pass PHP data to JavaScript

        function incrementProduct(id) {
            productCounts[id] = (productCounts[id] || 0) + 1;
            document.getElementById(`count-${id}`).innerText = productCounts[id];
        }

        function decrementProduct(id) {
            if (productCounts[id] && productCounts[id] > 0) {
                productCounts[id] -= 1;
                document.getElementById(`count-${id}`).innerText = productCounts[id];
            }
        }

        function checkout() {
            fetch("", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `checkout=true&updates=${JSON.stringify(productCounts)}`
            })
            .then(response => response.text())
            .then(() => {
                alert("Checkout successful!");
                location.reload(); // Reload the page to reset the state
            });
        }
    </script>
</head>
<body>
    <!-- Header -->
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

    <!-- Product Grid -->
    <div class="grid grid-cols-4 gap-4">
        <?php foreach ($products as $product): ?>
            <div id="<?= $product['product_id']; ?>" class="flex flex-col items-center mt-2 p-4 border border-gray-300 rounded">
                <img src="../images/<?= $product['image_path']; ?>" alt="Produk" class="w-32 h-32 mb-4">
                <h2 class="text-2xl font-bold mb-2"><?= htmlspecialchars($product['product_name']); ?></h2>
                <p class="text-gray-700">Stok Tersedia: <?= $product['stock']; ?></p>
                <p class="text-gray-700 mb-4">Price: Rp <?= $product['product_price']; ?></p>
                
                <div class="mt-4 flex gap-4 items-center">
                    <button onclick="decrementProduct(<?= $product['product_id']; ?>)" class="w-[30px] h-[30px] bg-red-400 text-white rounded hover:bg-red-700 duration-300">-</button>
                    <span id="count-<?= $product['product_id']; ?>" class="text-2xl">0</span>
                    <button onclick="incrementProduct(<?= $product['product_id']; ?>)" class="w-[30px] h-[30px] bg-blue-400 text-white rounded hover:bg-blue-700 duration-300">+</button>
                </div>

                <?php if ($product['stock'] == 0): ?>
                    <p class="text-red-500 font-extrabold">Product is Empty</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Checkout Button -->
    <div class="relative flex justify-center mt-8 gap-4">
        <button onclick="checkout()" class="text-xl w-[110px] h-[40px] bg-green-500 text-white rounded hover:bg-green-700 duration-300">
            Checkout
        </button>
        <button class="text-xl w-[110px] h-[40px] bg-blue-500 text-white rounded hover:bg-blue-700 duration-300">
            <a href="form_cetak.php">Print</a>
        </button>
    </div>
</body>
</html>

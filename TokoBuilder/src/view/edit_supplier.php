<?php
include '../logic/tools.php';
session_start();
if(!isset($_SESSION['username'])){
    header("Location: ../headerManager/login.php");
}

// Pastikan koneksi berhasil
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// dapatkan id supplier dari get request
$supplier_id = isset($_GET['supplier_id']) ? intval($_GET['supplier_id']) : 0;

// Fetch detail supplier
$supplier_query = $conn->query("SELECT * FROM Supplier WHERE supplier_id = $supplier_id");
if (!$supplier_query) {
    die("Query Supplier gagal: " . $conn->error);
}
$supplier = $supplier_query->fetch_assoc();

// Fetch all products
$product_query = $conn->query("SELECT * FROM Produk");
if (!$product_query) {
    die("Query Produk gagal: " . $conn->error);
}

// Fetch already assigned products
$assigned_products_query = $conn->query("SELECT product_id FROM produk_yang_disupply WHERE supplier_id = $supplier_id");
if (!$assigned_products_query) {
    die("Query Assigned Products gagal: " . $conn->error);
}
$assigned_products = [];
while ($row = $assigned_products_query->fetch_assoc()) {
    $assigned_products[] = $row['product_id'];
}

// Handle form submission to save the selected products
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['products'])) {
    $selected_products = $_POST['products'];
    
    // Pertama hapus semua produk yang disuplai oleh supplier ini
    $conn->query("DELETE FROM produk_yang_disupply WHERE supplier_id = $supplier_id");

    // Terus masukkan produk baru yang dipilih untuk supplier ini
    foreach ($selected_products as $product_id) {
        $conn->query("INSERT INTO produk_yang_disupply (product_id, supplier_id) VALUES ($product_id, $supplier_id)");
    }

    // Redirect ke suppliers.php
    header("Location: suppliers.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Products for Supplier</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
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

  <div class="container mx-auto py-6">
    <h2 class="text-2xl font-bold mb-4">Edit Products for Supplier: <?php echo htmlspecialchars($supplier['supplier_name']); ?></h2>

    <!-- Form Edit Products -->
    <form action="edit_supplier.php?supplier_id=<?php echo $supplier_id; ?>" method="POST">
      <h3 class="text-xl font-bold mb-4">Select Products</h3>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if ($product_query->num_rows > 0) : ?>
          <?php while ($product = $product_query->fetch_assoc()) : ?>
            <div class="bg-white p-4 rounded-lg shadow">
              <label class="flex items-center space-x-2">
                <input type="checkbox" name="products[]" value="<?php echo $product['product_id']; ?>" 
                <?php echo in_array($product['product_id'], $assigned_products) ? 'checked' : ''; ?>>
                <span><?php echo htmlspecialchars($product['product_name']); ?></span>
              </label>
            </div>
          <?php endwhile; ?>
        <?php else : ?>
          <p class="text-red-500">Tidak ada produk yang tersedia.</p>
        <?php endif; ?>
      </div>

      <div class="mt-4">
        <button type="submit" class="bg-purple-700 text-white px-4 py-2 rounded">Save Changes</button>
      </div>
    </form>
  </div>
</body>
</html>

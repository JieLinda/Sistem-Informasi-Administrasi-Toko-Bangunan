<?php
include '../logic/tools.php';
session_start();
if(!isset($_SESSION['username'])){
  header("Location: index.php");
}

// Menghapus supplier
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];

    // Pertama, hapus setiap row di table produk_yang disupply yang memiliki supplier_id yang dipilih
    $delete_products_query = "DELETE FROM produk_yang_disupply WHERE supplier_id = $delete_id";
    if ($conn->query($delete_products_query) === TRUE) {
        // Setelah itu hapus supplier
        $delete_supplier_query = "DELETE FROM Supplier WHERE supplier_id = $delete_id";
        if ($conn->query($delete_supplier_query) === TRUE) {
            echo "<script>alert('Supplier and associated products deleted successfully!'); window.location.href='suppliers.php';</script>";
        } else {
            echo "<script>alert('Error deleting supplier: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Error deleting associated products: " . $conn->error . "');</script>";
    }
}

// fetch semua supplier di database
$supplier_query = $conn->query("SELECT * FROM Supplier");

// menambah supplier baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_supplier'])) {
    $supplier_name = $conn->real_escape_string($_POST['supplier_name']);

    if (!empty($supplier_name)) {
        $insert_query = "INSERT INTO Supplier (supplier_name) VALUES ('$supplier_name')";
        if ($conn->query($insert_query) === TRUE) {
            echo "<script>alert('Supplier added successfully!'); window.location.href='suppliers.php';</script>";
        } else {
            echo "<script>alert('Error adding supplier: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Please enter a supplier name.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Suppliers</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
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

  <div class="container mx-auto py-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Suppliers</h2>

    <!-- Form menambah supplier baru -->
    <form method="POST" class="mb-6">
      <input type="text" name="supplier_name" placeholder="Supplier Name" class="border border-gray-300 rounded p-2" required>
      <button type="submit" name="add_supplier" class="bg-green-500 text-white rounded px-4 py-2 hover:bg-green-600">Add Supplier</button>
    </form>

    <!-- List semua supplier -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php while ($row = $supplier_query->fetch_assoc()) { 
        // Fetch semua produk yang disuplai oleh supplier
        $supplier_id = $row['supplier_id'];
        $product_query = $conn->query("SELECT p.product_name FROM Produk p
                                      JOIN produk_yang_disupply py ON p.product_id = py.product_id
                                      WHERE py.supplier_id = $supplier_id");

        // ambil produk yang disuplai secara spesifik
        $products = [];
        while ($product = $product_query->fetch_assoc()) {
            $products[] = $product;
        }
      ?>
        <div class="bg-white rounded-lg shadow p-4 relative">
          <h3 class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($row['supplier_name']); ?></h3>

          <div class="mt-4">
            <h4 class="font-semibold text-gray-700">Products:</h4>
            <?php if (count($products) > 0) { ?>
              <ul class="list-disc pl-5">
                <?php foreach ($products as $product) { ?>
                  <li><?php echo htmlspecialchars($product['product_name']); ?></li>
                <?php } ?>
              </ul>
            <?php } else { ?>
              <p>No products assigned to this supplier.</p>
            <?php } ?>
          </div>

          
          <div class="absolute top-4 right-4 flex space-x-2">
            <!-- Button Edit -->
            <a href="edit_supplier.php?supplier_id=<?php echo $row['supplier_id']; ?>" 
              class="bg-blue-500 text-white rounded w-8 h-8 hover:bg-blue-600 flex justify-center items-center">
              ✏
            </a>

            <!-- Button Delete -->
            <a href="suppliers.php?delete_id=<?php echo $row['supplier_id']; ?>" 
              class="bg-red-500 text-white rounded w-8 h-8 hover:bg-red-600 flex justify-center items-center" 
              onclick="return confirm('Are you sure you want to delete this supplier?');">
              🗑
            </a>
        </div>

        </div>
      <?php } ?>
    </div>
  </div>

</body>
</html>

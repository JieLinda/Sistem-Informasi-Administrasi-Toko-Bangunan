<?php
include '../logic/tools.php';
session_start();
if(!isset($_SESSION['username'])){
    header("Location: index.php");
}

ini_set('display_errors', 1);
error_reporting(E_ALL);



// Add Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $nama_produk = $conn->real_escape_string($_POST['nama_produk']);
    $stok = intval($_POST['stok']);
    $min_stock = intval($_POST['min_stock']);
    $harga_jual = floatval($_POST['harga_jual']);
    $imagePath = NULL;  // Default image path is NULL

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageName = $_FILES['image']['name'];
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageType = $_FILES['image']['type'];

        // Define allowed image types
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($imageType, $allowedTypes)) {
            // Create a unique name for the file
            $imageNewName = uniqid('', true) . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
            $imageUploadPath = '../images/' . $imageNewName;

            // Move the uploaded file to the Images directory
            if (move_uploaded_file($imageTmpName, $imageUploadPath)) {
                $imagePath = $imageUploadPath;  // Store the file path
            } else {
                echo "Failed to upload image.";
            }
        }
    }

    // Insert product data into the database with image path
    $sql = "INSERT INTO Produk (product_name, stock, min_stock, product_price, image_path) VALUES ('$nama_produk', $stok, $min_stock, $harga_jual, '$imagePath')";
    if ($conn->query($sql) === TRUE) {
        header("Location: products.php");
        exit();
    } else {
        echo "<div class='text-red-500 mb-4'>Error: " . $conn->error . "</div>";
    }
}

// Edit Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $product_id = intval($_POST['product_id']);
    $nama_produk = $conn->real_escape_string($_POST['nama_produk']);
    $stok = intval($_POST['stok']);
    $harga_jual = floatval($_POST['harga_jual']);
    $imagePath = $_POST['existing_image_path']; // Use the existing image path by default

    // Check if a new image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageName = $_FILES['image']['name'];
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageType = $_FILES['image']['type'];

        // Define allowed image types
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($imageType, $allowedTypes)) {
            // Create a unique name for the file
            $imageNewName = uniqid('', true) . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
            $imageUploadPath = 'Images/' . $imageNewName;

            // Move the uploaded file to the Images directory
            if (move_uploaded_file($imageTmpName, $imageUploadPath)) {
                $imagePath = $imageUploadPath;  // Store the new file path
            } else {
                echo "Failed to upload image.";
            }
        }
    }

    // Update product data in the database, including image path
    $sql = "UPDATE Produk SET product_name = '$nama_produk', stock = $stok, product_price = $harga_jual, image_path = '$imagePath' WHERE product_id = $product_id";
    if ($conn->query($sql) === TRUE) {
        header("Location: products.php");
        exit();
    } else {
        echo "<div class='text-red-500 mb-4'>Error updating product: " . $conn->error . "</div>";
    }
}

// Delete Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    if ($delete_id > 0) {
        // Deleting dependent records first
        $sql_delete_dependent = "DELETE FROM produk_yang_disupply WHERE product_id = $delete_id";
        if ($conn->query($sql_delete_dependent) === TRUE) {
            // Now delete the product
            $sql = "DELETE FROM Produk WHERE product_id = $delete_id";
            if ($conn->query($sql) === TRUE) {
                header("Location: products.php");
                exit();
            } else {
                echo "<div class='text-red-500 mb-4'>Error deleting product: " . $conn->error . "</div>";
            }
        } else {
            echo "<div class='text-red-500 mb-4'>Error deleting dependent records: " . $conn->error . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src='tailwind.config.js'></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function openModal(productId, productName, stock, price, imagePath) {
            document.getElementById('product_id').value = productId;
            document.getElementById('nama_produk').value = productName;
            document.getElementById('stok').value = stock;
            document.getElementById('harga_jual').value = price;
            document.getElementById('existing_image_path').value = imagePath; // Set the existing image path

            console.log(imagePath)
            // Show the image preview if it exists
            if (imagePath) {
                document.getElementById('image_preview').src = "../images/" + imagePath;
                document.getElementById('image_preview').style.display = 'block';
            } else {
                document.getElementById('image_preview').style.display = 'none';
            }
            
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
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

  <!-- Add Product Form -->
  <div class="container mx-auto py-6">
    <h2 class="text-2xl font-bold mb-4">Products</h2>

    <form action="products.php" method="POST" enctype="multipart/form-data" class="mb-6 bg-white p-6 rounded shadow">
      <h3 class="text-xl font-bold mb-4">Add Product</h3>
      <input type="text" name="nama_produk" placeholder="Product Name" required class="w-full mb-3 p-2 border rounded" />
      <input type="number" name="stok" placeholder="Stock" required class="w-full mb-3 p-2 border rounded" />
      <input type="number" name="harga_jual" placeholder="Sale Price" required class="w-full mb-3 p-2 border rounded" />
      <input type="number" name="min_stock" placeholder="Minimum Stock" required class="w-full mb-3 p-2 border rounded" />
      <input type="file" name="image" accept="image/*" class="w-full mb-3 p-2 border rounded" />
      <button type="submit" name="add_product" class="bg-purple-700 hover:bg-purple-400 text-white px-4 py-2 rounded">Add Product</button>
    </form>

    <!-- Product List Table -->
    <table class="table-auto w-full bg-white rounded shadow">
      <thead class="bg-gray-200">
        <tr>
          <th class="px-4 py-2 text-left">Product Name</th>
          <th class="px-4 py-2 text-left">Stock</th>
          <th class="px-4 py-2 text-left">Sale Price</th>
          <th class="px-4 py-2 text-left">Image</th>
          <th class="px-4 py-2 text-left">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $result = $conn->query("SELECT * FROM Produk");
        while ($row = $result->fetch_assoc()) {
            $harga_jual = "RP " . number_format($row['product_price'], 0, ',', '.');
            $imagePath = $row['image_path'] ? $row['image_path'] : 'Images/acer predator.png';  // Default image if no image uploaded
            echo "<tr>
                    <td class='border px-4 py-2'>{$row['product_name']}</td>
                    <td class='border px-4 py-2'>{$row['stock']}</td>
                    <td class='border px-4 py-2'>{$harga_jual}</td>
                    <td class='border px-4 py-2'><img src='../images/$imagePath' alt='Product Image' width='100'></td>
                    <td class='border px-4 py-2'>
                        <div class='flex space-x-2'>
                            <button 
                                onclick=\"openModal('{$row['product_id']}', '{$row['product_name']}', '{$row['stock']}', '{$row['product_price']}', '{$row['image_path']}')\" 
                                class='bg-green-500 hover:bg-green-800 text-white px-4 py-2 rounded'>
                                Edit
                            </button>
                            <form action='products.php' method='POST' onsubmit='return confirm(\"Are you sure you want to delete this product?\");'>
                                <input type='hidden' name='delete_id' value='{$row['product_id']}' />
                                <button type='submit' class='bg-red-500 hover:bg-red-800 text-white px-4 py-2 rounded'>Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  <!-- Edit Modal -->
  <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded shadow-lg w-96">
      <h2 class="text-xl font-bold mb-4">Edit Product</h2>
      <form action="products.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="product_id" id="product_id">
        <input type="hidden" name="existing_image_path" id="existing_image_path">
        <div class="mb-4">
          <label class="block text-gray-700">Product Name</label>
          <input type="text" name="nama_produk" id="nama_produk" class="w-full p-2 border rounded" required>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Stock</label>
          <input type="number" name="stok" id="stok" class="w-full p-2 border rounded" required>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Sale Price</label>
          <input type="number" name="harga_jual" id="harga_jual" class="w-full p-2 border rounded" required>
        </div>
        <!-- <div class="mb-4">
          <label class="block text-gray-700">Min Stock</label>
          <input type="number" name="min_stock" id="min_stock" class="w-full p-2 border rounded" required>
        </div> -->
        <div class="mb-4">
          <label class="block text-gray-700">Product Image</label>
          <input type="file" name="image" accept="image/*" class="w-full mb-3 p-2 border rounded">
          <img id="image_preview" src="" alt="Image Preview" width="100" class="mb-4">
        </div>
        <div class="flex justify-end space-x-4">
          <button type="button" onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
          <button type="submit" name="edit_product" class="bg-purple-700 hover:bg-purple-900 text-white px-4 py-2 rounded">Save</button>
        </div>
      </form>
    </div>
  </div>

</body>
</html>

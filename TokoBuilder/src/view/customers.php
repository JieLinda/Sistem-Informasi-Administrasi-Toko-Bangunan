<?php
// Include database connection
session_start();
require '../logic/tools.php';
if(!isset($_SESSION['username'])){
    header("Location: index.php");
}


if (isset($_POST['add_customer'])) {
    // Add customer logic
    $nama = $_POST['nama'];
    $no_telepon = $_POST['no_telepon'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $conn->query("INSERT INTO customer (customer_name, phone_number, email, address) VALUES ('$nama', '$no_telepon', '$email', '$address')");

    $_POST = [];
}

if (isset($_POST['edit_customer'])) {
    // Edit customer logic
    $customer_id = $_POST['customer_id'];
    $nama = $_POST['nama'];
    $no_telepon = $_POST['no_telepon'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $conn->query("UPDATE customer SET customer_name = '$nama', phone_number = '$no_telepon', email = '$email', address = '$address' WHERE customer_id = $customer_id");
}

if (isset($_POST['delete_id'])) {
    // Delete customer logic
    $customer_id = $_POST['delete_id'];
    $conn->query("DELETE FROM customer WHERE customer_id = $customer_id");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function openModal(customerId, name, phone, email, address) {
            console.log("test")
            document.getElementById('customer_id').value = customerId;
            document.getElementById('nama').value = name;
            document.getElementById('no_telepon').value = phone;
            document.getElementById('email').value = email;
            document.getElementById('address').value = address;

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

  <!-- Add Customer Form -->
  <div class="container mx-auto py-6">
    <h2 class="text-2xl font-bold mb-4">Customers</h2>

    <form action="customers.php" method="POST" class="mb-6 bg-white p-6 rounded shadow">
      <h3 class="text-xl font-bold mb-4">Add Customer</h3>
      <input type="text" name="nama" placeholder="Customer Name" required class="w-full mb-3 p-2 border rounded" />
      <input type="text" name="no_telepon" placeholder="Phone Number" required class="w-full mb-3 p-2 border rounded" />
      <input type="email" name="email" placeholder="Email" class="w-full mb-3 p-2 border rounded" />
      <textarea name="address" placeholder="Address" class="w-full mb-3 p-2 border rounded"></textarea>
      <button type="submit" name="add_customer" class="bg-purple-700 hover:bg-purple-500 text-white px-4 py-2 rounded">Add Customer</button>
    </form>

    <!-- Customer List Table -->
    <table class="table-auto w-full bg-white rounded shadow">
      <thead class="bg-gray-200">
        <tr>
          <th class="px-4 py-2 text-left">Customer Name</th>
          <th class="px-4 py-2 text-left">Phone Number</th>
          <th class="px-4 py-2 text-left">Email</th>
          <th class="px-4 py-2 text-left">Address</th>
          <th class="px-4 py-2 text-left">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = $conn->query("SELECT * FROM customer");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td class='border px-4 py-2'>{$row['customer_name']}</td>
                    <td class='border px-4 py-2'>{$row['phone_number']}</td>
                    <td class='border px-4 py-2'>{$row['email']}</td>
                    <td class='border px-4 py-2'>{$row['address']}</td>
                    <td class='border px-4 py-2'>
                        <div class='flex space-x-2'>
                            <button 
                                onclick=\"openModal('{$row['customer_id']}', '{$row['customer_name']}', '{$row['phone_number']}', '{$row['email']}', '{$row['address']}')\" 
                                class='bg-green-500 hover:bg-green-800 text-white px-4 py-2 rounded'>
                                Edit
                            </button>
                            <form action='customers.php' method='POST' onsubmit='return confirm(\"Are you sure you want to delete this customer?\");'>
                                <input type='hidden' name='delete_id' value='{$row['customer_id']}' />
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
      <h2 class="text-xl font-bold mb-4">Edit Customer</h2>
      <form action="customers.php" method="POST">
        <input type="hidden" name="customer_id" id="customer_id">
        <div class="mb-4">
          <label class="block text-gray-700">Customer Name</label>
          <input type="text" name="nama" id="nama" class="w-full p-2 border rounded" required>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Phone Number</label>
          <input type="text" name="no_telepon" id="no_telepon" class="w-full p-2 border rounded" required>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Email</label>
          <input type="email" name="email" id="email" class="w-full p-2 border rounded">
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Address</label>
          <textarea name="address" id="address" class="w-full p-2 border rounded"></textarea>
        </div>
        <div class="flex justify-end space-x-4">
          <button type="button" onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
          <button type="submit" name="edit_customer" class="bg-purple-500 hover:bg-purple-800 text-white px-4 py-2 rounded">Save</button>
        </div>
      </form>
    </div>
  </div>

</body>
</html>

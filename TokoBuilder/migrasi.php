<?php
// Memastikan file ini menerima parameter nota_id melalui URL
if (isset($_GET['nota_id']) && isset($_GET['nama_supplier'])) {
    $nota_id = htmlspecialchars($_GET['nota_id']); // Melindungi input dengan htmlspecialchars
    $nama_supplier = htmlspecialchars($_GET['nama_supplier']); // Melindungi input dengan htmlspecialchars
} else {
    // Jika salah satu variabel tidak diterima, redirect ke halaman sebelumnya atau tampilkan pesan error
    die('Error: nota_id atau nama_supplier tidak ditemukan.');
}

// Koneksi ke database
require_once('db.php');

// Query untuk mendapatkan data terkait nota_id
$query = "SELECT 
                ROW_NUMBER() OVER (ORDER BY or_.order_restock_id) AS No,
                p.nama_produk AS 'Product Name',
                or_.order_restock_price AS 'Price',
                or_.order_restock_qty AS 'QTY',
                or_. order_restock_delivered_qty AS 'Delivered',
                (or_.order_restock_price * or_.order_restock_qty) AS 'Total Payment',
                or_.order_restock_delivery_status AS 'Status'
            FROM 
                Order_Restock or_
            JOIN 
                Produk p ON or_.produk_id = p.produk_id
            JOIN 
                Nota_Pembelian np ON np.nota_id = or_.nota_id
            WHERE 
                np.nota_id = ?";

$stmt = $con->prepare($query);
if ($stmt === false) {
    die('Error preparing statement: ' . $con->error);
}

// Bind the parameter and execute the query
$stmt->bind_param("i", $nota_id);
$stmt->execute();
$result = $stmt->get_result();

// Periksa apakah data ditemukan
if ($result->num_rows === 0) {
    die('Nota ID tidak ditemukan dalam database.');
}

// Ambil data dari query
$nota_data = $result->fetch_assoc();

// Mengambil variabel dari URL dengan validasi
$nota_id = isset($_GET['nota_id']) ? $_GET['nota_id'] : null;
$nama_supplier = isset($_GET['nama_supplier']) ? $_GET['nama_supplier'] : 'Unknown Supplier';

// Debugging - untuk melihat apakah parameter diterima dengan benar
// echo "Nota ID: " . $nota_id . "<br>";
// echo "Nama Supplier: " . $nama_supplier . "<br>";
?>

// Tutup koneksi
$stmt->close();
$con->close();

// Pastikan variabel $notaID diambil dari parameter, misalnya dari URL atau formulir
// $notaID = isset($_GET['notaID']) ? intval($_GET['notaID']) : 0; // Pastikan untuk memvalidasi input

?>

<!DOCTYPE HTML>
<html lang="en">
 <head>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
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
    <img alt="User profile picture" class="rounded-full" height="40" src="https://storage.googleapis.com/a1aa/image/6SeyLSBlUxTLfEygcTCmONTntQMZamEDRazHWvz5G280KHzTA.jpg" width="40"/>
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
        <i class="fas fa-box mr-3"></i>
        <span>Product</span>
       </li>
       <li class="flex items-center px-10 py-3 text-gray-600 hover:bg-gray-200">
        <i class="fas fa-truck mr-3"></i>
        <span>Supplier</span>
       </li>
       <li class="flex items-center px-10 py-3 text-gray-600 hover:bg-gray-200">
        <i class="fas fa-exchange-alt mr-3"></i>
        <span>Transaction</span>
       </li>
       <li class="flex items-center px-10 py-3 text-gray-600 bg-gray-100 text-gray-600">
        <i class="fas fa-shopping-cart mr-3"></i>
        <span>Purchase</span>
       </li>
      </ul>
     </nav>
    </div>
    <!-- Main Content -->
    <div class="w-4/5">
     <main class="p-6">
      <div class="bg-white p-6 rounded-lg shadow-lg">
       <h2 class="text-2xl font-semibold text-purple-600 mb-4">
        UD Makmur Abadi
       </h2>
       <div class="flex items-center mb-6">
       <p class="text-lg text-gray-600 mt-2">
            Update Nota:
            <span class="text-blue-600">
                <?php echo htmlspecialchars($nota_id); ?> [<?php echo htmlspecialchars($nama_supplier); ?>]
            </span>
        </p>
       </div>
       <div class="mt-6 bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
         <button class="bg-purple-600 text-white px-4 py-2 rounded-full">
          Update Delivered
         </button>
         <div class="relative">
          <input class="border border-gray-300 rounded-full pl-10 pr-4 py-2" placeholder="Search for items" type="text"/>
          <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
         </div>
       </div>
       <!-- Tabel 1 -->
       <table class="w-full text-left" id="shipmentTable1">
        <thead>
         <tr class="text-gray-600">
          <th class="py-2">No</th>
          <th class="py-2">Product Name</th>
          <th class="py-2">Price</th>
          <th class="py-2">QTY</th>
          <th class="py-2">Delivered</th>
          <th class="py-2">Total Payment</th>
          <th class="py-2">Status</th>
          <th class="py-2">Update QTY</th>
         </tr>
        </thead>
        <tbody class="text-gray-700">
    <?php
    // Asumsikan $result adalah hasil query yang telah diambil dari database
    while ($row = mysqli_fetch_assoc($result)) {
        $statusClass = ($row['Status'] == 'on process') ? 'text-orange-500 bg-orange-100' : 'text-green-500 bg-green-100';
        ?>
        <tr class="border-t">
            <td class="py-2"><?php echo $row['No']; ?></td>
            <td class="py-2"><?php echo $row['Product Name']; ?></td>
            <td class="py-2"><?php echo number_format($row['Price'], 2, '.', ','); ?></td>
            <td class="py-2"><?php echo $row['QTY']; ?></td>
            <td class="py-2"><?php echo $row['Delivered']; ?></td>
            <td class="py-2"><?php echo number_format($row['Total Payment'], 2, '.', ','); ?></td>
            <td class="py-2">
                <span class="px-2 py-1 rounded-full <?php echo $statusClass; ?>"><?php echo ucfirst($row['Status']); ?></span>
            </td>
            <td class="py-2">
                <input class="border border-gray-300 rounded-full w-16 text-center" type="number" min="0" step="1" value="0"/>
            </td>
        </tr>
        <?php
    }
    ?>
</tbody>
       </table>

      </div>
      <div class="mt-6 flex justify-end">
        <button class="bg-purple-600 text-white px-4 py-2 rounded-full">
         To be returned
        </button>
       </div>
     </main>
    </div>
   </div>
  </div>
 </body>
</html>

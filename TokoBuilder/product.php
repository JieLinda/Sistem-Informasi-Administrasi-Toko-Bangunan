<?php 
require 'authen.php';

if (isset($_SESSION['message'])) {
    echo "<script>alert('".$_SESSION['message']."');</script>";
    unset($_SESSION['message']);
}

if ( !isset($_SESSION['username']) ){
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

$query = "SELECT * FROM produk WHERE username = '$username'";
$result = mysqli_query($con, $query);
if (!$result) {
    die('Query Error: ' . mysqli_error($con));
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
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
 </head>
 <body class="bg-gray-100">
  <div class="flex flex-col">
   <!-- Full Width Header -->
   <header class="bg-black text-white py-4 px-6 flex justify-between items-center w-full">
    <h1 class="text-xl font-semibold">
    Reseller Information System
    </h1>
    
    <a class="nav-link text-white" href="logout.php">Logout</a>
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
      <li class="flex items-center px-10 py-3 text-gray-600 bg-gray-100 text-gray-600">
            <a href="product.php" class="flex items-center">
                <i class="fas fa-box mr-3"></i>
                <span>Product</span>
            </a>
        </li>
        <li class="flex items-center px-10 py-3 text-gray-600 hover:bg-gray-200">
        <a href="supplier.php" class="flex items-center">    
            <i class="fas fa-truck mr-3"></i>
            <span>Supplier</span>
        </li>
        <li class="flex items-center px-10 py-3 text-gray-600 hover:bg-gray-200">
            <a href="sales/index.php" class="flex items-center">
                <i class="fas fa-exchange-alt mr-3"></i>
                <span>Sales</span>
            </a>
        </li>
        <li class="flex items-center px-10 py-3 text-gray-600 hover:bg-gray-200">
            <a href="purchase/index.php" class="flex items-center">
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
        Welcome, <?=$_SESSION['username']?>
       </h2>
       <div class="flex items-center mb-6">
        <h3 class="text-xl font-semibold text-gray-700">
         Data Produk
        </h3>
       </div>
       <div class="flex justify-between items-center mb-4">
        
       <a class="bg-purple-600 text-white px-4 py-2 rounded-lg" href="add_product.php">Add Product</a>
        
       <form action="" method="post">
        <div class="flex items-center">
         <input name="pro" class="border border-gray-300 rounded-lg px-3 py-2" autofocus placeholder="Cari nama produk.." type="text" id="keyword">
        </div>
       </div>

       
       <!-- Tabel Shipment -->
       <div id="conpro">
       <table id="shipmentTable" class="w-full text-left" style="display: table;">
        <thead>
         <tr class="text-gray-600">
          <th class="py-2">No</th>
          <th class="py-2">Nama Produk</th>
          <th class="py-2">Stok</th>
          <th class="py-2">Harga Jual</th>
          <th class="py-2">Action</th>
         </tr>
        </thead>
        <tbody class="text-gray-700">
        <?php 
        $counter = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            
        ?>
        <tr class="border-b">
            <td class="py-2"><?php echo $counter++; ?></td>
            <td class="py-2"><?php echo $row['nama_produk']; ?></td>
            <td class="py-2"><?php echo $row['stok']; ?></td>
            <td class="py-2"><?php echo $row['harga_jual']; ?></td>
            <td>
            <!-- Form untuk Update -->
            <a class="btn btn-primary" href="update_product.php?id=<?= $row["produk_id"]; ?>">Update</a>
            <?php $_SESSION["id"] = $row["produk_id"] ?>
            <!-- Form untuk Delete -->
             <a class="btn btn-danger" href="delete_product.php?id=<?= $row["produk_id"]; ?>" onclick="return confirm('Yakin?')">Delete</a>
        </td>
        </tr>
        <?php } ?>
        </tbody>
       </table>
       </div>
       
       <script src="js/scriptpro.js"></script>

      </div>
     </main>
    </div>
   </div>
  </div>
 </body>
</html>
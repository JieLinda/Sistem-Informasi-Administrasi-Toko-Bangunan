<?php
include '../logic/tools.php';
session_start();
if(!isset($_SESSION['username'])){
    header("Location: ../headerManager/login.php");
}

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$selected_date = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$total_keuntungan = 0;

$keuntungan_query = $conn->prepare(
    "SELECT p.product_name, sum(j.total) AS keuntungan
    FROM jurnal j join produk p on j.product_id = p.product_id
    WHERE j.transaction_date = ? group by p.product_name"
);
$keuntungan_query->bind_param('s', $selected_date);
$keuntungan_query->execute();
$result = $keuntungan_query->get_result();
$keuntungan_list = [];
while ($row = $result->fetch_assoc()) {
    $keuntungan_list[] = $row;
    $total_keuntungan += $row['keuntungan'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Catatan Keuntungan</title>
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
    <h2 class="text-2xl font-bold mb-4">Create Catatan Keuntungan</h2>

    <form method="GET" action="">
      <label for="tanggal" class="block text-lg font-medium mb-2">Pilih Tanggal:</label>
      <input type="date" id="tanggal" name="tanggal" value="<?php echo htmlspecialchars($selected_date); ?>" class="border border-gray-300 p-2 rounded-lg w-full md:w-1/3">
      <button type="submit" class="bg-purple-700 text-white px-4 py-2 rounded mt-3">Submit</button>
    </form>

    <div class="mt-6">
      <h3 class="text-xl font-bold mb-4">Keuntungan Kotor untuk Tanggal: <?php echo htmlspecialchars($selected_date); ?></h3>
      <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
        <thead class="bg-purple-700 text-white">
          <tr>
            <th class="py-3 px-6 text-left">Nama Produk</th>
            <th class="py-3 px-6 text-right">Keuntungan (Rp)</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($keuntungan_list) > 0) : ?>
            <?php foreach ($keuntungan_list as $keuntungan) : ?>
              <tr class="border-b">
                <td class="py-3 px-6"><?php echo htmlspecialchars($keuntungan['product_name']); ?></td>
                <td class="py-3 px-6 text-right"><?php echo number_format($keuntungan['keuntungan'], 2, ',', '.'); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else : ?>
            <tr>
              <td colspan="2" class="py-3 px-6 text-center">Tidak ada data untuk tanggal ini.</td>
            </tr>
          <?php endif; ?>
        </tbody>
        <tfoot>
          <tr class="bg-gray-200">
            <th class="py-3 px-6 text-left">Total Keuntungan</th>
            <th class="py-3 px-6 text-right">Rp <?php echo number_format($total_keuntungan, 2, ',', '.'); ?></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</body>
</html>

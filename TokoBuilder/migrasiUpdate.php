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


$querySUM = "SELECT nota_id, SUM(hutang_total) AS total_hutang
            FROM hutang
            WHERE nota_id = ?
            GROUP BY nota_id;";

$stmt = $con->prepare($query);
$stmt2 = $con->prepare($querySUM);
if ($stmt === false) {
    die('Error preparing statement: ' . $con->error);
}
if ($stmt2 === false) {
    die('Error preparing statement: ' . $con->error);
}

// Bind the parameter and execute the query
$stmt->bind_param("i", $nota_id);
$stmt->execute();
$result = $stmt->get_result();

$stmt2->bind_param("i", $nota_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$totalUnpaidRow = $result2->fetch_assoc();
$totalUnpaid = $totalUnpaidRow['total_hutang'] ?? 0;



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


// Tutup koneksi
$stmt->close();
$con->close();

// Pastikan variabel $notaID diambil dari parameter, misalnya dari URL atau formulir
// $notaID = isset($_GET['notaID']) ? intval($_GET['notaID']) : 0; // Pastikan untuk memvalidasi input

?>
<!-- Script -->


<!DOCTYPE HTML>
<html lang="en">
    <head>
    <?php include 'head.php'; ?>
    <style>
    body {
                font-family: 'Inter', sans-serif;
            }
    </style>

    <!-- Script -->
    <!-- <script>
        const openModalButton = document.getElementById('openModal');
        const closeModalButton = document.getElementById('closeModal');
        const modal = document.getElementById('modal');

        // Open modal
        openModalButton.addEventListener('click', () => {
            modal.classList.remove('hidden');
        });

        // Close modal
        closeModalButton.addEventListener('click', () => {
            modal.classList.add('hidden');
        });

        // Close modal on overlay click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    </script> -->

    <script>
        // Ambil elemen modal dan tombol
        const modal = document.getElementById('modal');
        const openModalButton = document.getElementById('openModalButton'); // Seleksi tombol edit (ikon pensil)
        const closeModalButton = document.getElementById('cancelButton');
        const saveButton = document.getElementById('saveButton');

        // Event listener untuk membuka modal
        openModalButton.addEventListener('click', () => {
            modal.classList.remove('hidden');
        });

        // Event listener untuk menutup modal
        closeModalButton.addEventListener('click', () => {
            modal.classList.add('hidden');
        });

        // Event listener untuk menyimpan data dan menutup modal (kustomisasi sesuai kebutuhan)
        saveButton.addEventListener('click', () => {
            const inputValue = document.getElementById('inputText').value;
            console.log('Saved value:', inputValue);
            modal.classList.add('hidden');
        });

        // Tutup modal saat klik di luar modal
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });


    </script>

    
    </head>
    <body class="bg-gray-100">
        <div class="flex flex-col">
            <!-- Full Width Header -->
            <header class="bg-black text-white py-4 px-6 flex justify-between items-center w-full">
                <h1 class="text-xl font-semibold">
                Reseller Information System
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
                        <a href="sales.php" class="flex items-center">
                            <i class="fas fa-exchange-alt mr-3"></i>
                            <span>Sales</span>
                        </a>
                    </li>
                    <li class="flex items-center px-10 py-3 text-gray-600 bg-gray-100 text-gray-600">
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
                            <!-- Bagian kiri -->
                            <div class="flex gap-4">
                                <button class="bg-purple-600 text-white px-4 py-2 rounded-full">
                                    Save changes
                                </button>
                                <button class="bg-purple-600 text-white px-4 py-2 rounded-full">
                                    Update Delivered
                                </button>
                            </div>
                            <!-- Bagian kanan -->
                            <div class="relative">
                                <input class="border border-gray-300 rounded-full pl-10 pr-4 py-2" placeholder="Search for items" type="text"/>
                                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                            </div>
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
                        <th class="py-2">Total Price</th>
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
                                <!-- Tambahkan id pada elemen editButton -->
                                <td class="py-2">
                                    <!-- <a id="openModalButton" class="editButton">
                                        <i class="fas fa-pencil-alt text-gray-500"></i>
                                    </a> -->
                                    <button id="openModal" class="bg-purple-600 text-white px-2 py-1 rounded-full">
                                        Return
                                    </button>
                                </td>


                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>

                </div>
                <p class="text-lg text-gray-600 mt-2">
                    Total Unpaid: <?php echo number_format($totalUnpaid, 2); ?>
                </p>

                <div class="mt-6 flex justify-end">
                        <input class="border border-gray-200 rounded-full pl-10 py-1" placeholder="Paid amount" type="text"/>
                </div>
            </div>
            
        </div>
        </div>

        <!-- Modal untuk input teks -->
        <div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-lg font-semibold mb-4">Update Quantity</h3>
                <input type="text" id="inputText" class="border border-gray-300 rounded w-full px-2 py-1 mb-4" placeholder="Enter details...">
                <div class="flex justify-end">
                    <button id="saveButton" class="bg-blue-600 text-white px-4 py-2 rounded mr-2">Save</button>
                    <button id="cancelButton" class="bg-gray-400 text-white px-4 py-2 rounded">Cancel</button>
                </div>
            </div>
        </div>


    </body>
</html>

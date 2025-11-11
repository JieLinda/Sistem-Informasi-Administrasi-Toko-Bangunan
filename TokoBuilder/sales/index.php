<?php
define("VALID_ACCESS", 1);

require_once '../db.php';

require_once '../authen.php';

if ( !isset($_SESSION['username']) ){
    header("Location: login.php");
    exit();
}
require_once '../_repositories/queries.php';

try {
    $result = get_sales_summary($con, $username);
} catch (Exception $e) {
    die($e->getMessage());
}

// Close the connection
$con->close();
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
    <?php require_once '../head.php'; ?>
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
                Reseller Information System
            </h1>
            
        </header>
        <div class="flex">
            <!-- Sidebar -->
            <div class="w-1/5 bg-white h-screen shadow-lg">
                <div class="flex flex-col items-center py-10">
                    
                    <h2 class="text-lg font-semibold">
                        Admin
                    </h2>
                    <span class="text-sm text-green-500 bg-green-100 px-2 py-1 rounded-full">
                        Administrator
                    </span>
                </div>
                <?php require_once '../includes/sidebar.php' ?>
            </div>
            <!-- Main Content -->
            <div class="w-4/5">
                <main class="p-6">
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h2 class="text-2xl font-semibold text-purple-600 mb-4">
                            UD Makmur Abadi
                        </h2>
                        <div class="flex items-center mb-6">
                            <h3 class="text-xl font-semibold text-gray-700">
                                Data Sales
                            </h3>
                        </div>
                        <div class="flex justify-between items-center mb-4">
                            <a href="<?= base_url() ?>/sales/form.php" class="flex items-center">
                                <button class="bg-purple-600 text-white px-4 py-2 rounded-lg">
                                    Add Sales
                                </button>
                            </a>
                            <a href="<?= base_url() ?>/cetak/form.php" class="flex items-center">
                                <button class="bg-purple-600 text-white px-4 py-2 rounded-lg">
                                    Generate Journals
                                </button>
                            </a>

                        </div>
                        <!-- Tabel Shipment -->
                        <table id="shipmentTable" class="w-full text-left" style="display: table;">
                            <thead>
                                <tr class="text-gray-600">
                                    <th class="py-2">No</th>
                                    <th class="py-2">ID Transaksi</th>
                                    <th class="py-2">Tanggal</th>
                                    <th class="py-2">Total Transaksi</th>
                                    <th class="py-2">Total QTY</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                <?php
                                $counter = 1;
                                foreach ($result as $row): ?>
                                    <tr class="border-b">
                                        <td class="py-2"><?= $counter++; ?></td>
                                        <td class="py-2"><?= $row['catatan_id']; ?></td>
                                        <td class="py-2"><?= $row['transaction_date']; ?></td>
                                        <td class="py-2"><?= number_format($row['total_transaksi'], 0, ',', '.'); ?></td>
                                        <td class="py-2"><?= $row['total_qty']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </main>
            </div>
        </div>
    </div>
</body>

</html>
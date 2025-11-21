<?php
// require '../authen.php';

// if ( !isset($_SESSION['username']) ){
//     header("Location: login.php");
//     exit();
// }

require '../logic/tools.php';

$query = "SELECT np.nota_id, s.supplier_name, np.tanggal, np.total AS total_payment, 
            SUM(or_.order_restock_qty) AS qty, 
            CASE 
                WHEN SUM(or_.order_restock_delivery_status = 'on process') > 0 THEN 'on process'
                ELSE 'delivered'
            END AS status_delivery
            FROM nota_pembelian np
            JOIN order_restock or_ ON np.nota_id = or_.nota_id
            JOIN supplier s ON np.supplier_id = s.supplier_id
            GROUP BY np.nota_id, s.supplier_name, np.tanggal, np.total";
$result = mysqli_query($conn, $query);
if (!$result) {
    die('Query Error: ' . mysqli_error($conn));
}

// Query untuk mengambil data pembayaran dengan kriteria yang diminta
$query2 = "SELECT np.nota_id, s.supplier_name, np.tanggal, np.total AS total_payment, 
            h.deadline, h.debt_amount,
            CASE 
                WHEN h.debt_amount IS NULL THEN 'Paid' 
                WHEN h.debt_amount = np.total THEN 'Unpaid' 
                ELSE 'Partial' 
            END AS status
            FROM nota_pembelian np
            JOIN supplier s ON np.nota_id = s.supplier_id
            LEFT JOIN hutang h ON np.nota_id = h.nota_id";

$result2 = mysqli_query($conn, $query2);
if (!$result2) {
    die('Query Error: ' . mysqli_error($conn));
}

// Close the connection
$conn->close();
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src='tailwind.config.js'></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    <script>
        function showTable() {
            const selectedOption = document.getElementById('dropdown').value;
            const shipmentTable = document.getElementById('shipmentTable');
            const paymentTable = document.getElementById('paymentTable');

            if (selectedOption === 'shipment') {
                shipmentTable.style.display = 'table';
                paymentTable.style.display = 'none';
            } else if (selectedOption === 'payment') {
                shipmentTable.style.display = 'none';
                paymentTable.style.display = 'table';
            }
        }
    </script>
</head>

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
        </ul>
        <a href="../logout.php" class="hover:underline">Logout</a>
      </nav>
    </div>
  </header>

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
           
            <!-- Main Content -->
            <div class="w-4/5">
                <main class="p-6">
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h2 class="text-2xl font-semibold text-purple-600 mb-4">
                            UD Makmur Abadi
                        </h2>
                        <div class="flex items-center mb-6">
                            <h3 class="text-xl font-semibold text-gray-700">
                                Data Pembelian
                            </h3>
                        </div>
                        <div class="flex justify-between items-center mb-4">
                            <a href="form_nota.php" class="bg-purple-600 text-white px-4 py-2 rounded-lg">
                                Add Catatan Pembelian
                            </a>
                        </div>

                        <!-- Tabel Shipment -->
                        <table id="shipmentTable" class="w-full text-left" style="display: table;">
                            <thead>
                                <tr class="text-gray-600">
                                    <th class="py-2">No</th>
                                    <th class="py-2">Bill ID</th>
                                    <th class="py-2">Supplier</th>
                                    <th class="py-2">Tanggal</th>
                                    <th class="py-2">Total Payment</th>
                                    <th class="py-2">QTY</th>
                                    <!-- <th class="py-2">Status</th> -->
                                    <th class="py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                <?php
                                $counter = 1;
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $status_class = $row['status_delivery'] === 'on process'
                                        ? 'text-orange-500 bg-orange-100 px-2 py-1 rounded-full'
                                        : 'text-green-500 bg-green-100 px-2 py-1 rounded-full';
                                ?>
                                    <tr class="border-b">
                                        <td class="py-2"><?php echo $counter++; ?></td>
                                        <td class="py-2"><?php echo $row['nota_id']; ?></td>
                                        <td class="py-2"><?php echo $row['supplier_name']; ?></td>
                                        <td class="py-2"><?php echo $row['tanggal']; ?></td>
                                        <td class="py-2"><?php echo $row['total_payment']; ?></td>
                                        <td class="py-2"><?php echo $row['qty']; ?></td>
                                        <!-- <td class="py-2">
                                            <span class="<?php echo $status_class; ?>">
                                                <?php echo $row['status_delivery']; ?>
                                            </span>
                                        </td> -->
                                        <td class="py-2">
                                        <a href="detail_nota.php?nota_id=<?php echo $row['nota_id']; ?>&supplier_name=<?php echo urlencode($row['supplier_name']); ?>" 
                                          class="bg-blue-500 text-white rounded w-8 h-8 hover:bg-blue-600 flex justify-center items-center">
                                          ✏
                                        </a>
                          
                                        </td>
                                        <td class="py-2">
                                            <a class="btn btn-danger" href="delete_nota.php?id=<?= $row['nota_id']; ?>" onclick="return confirm('Yakin?')">Delete</a>
                                            </a>
                                        </td>
                                        
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <!-- Tabel Payment -->
                        <table id="paymentTable" class="w-full text-left" style="display: none;">
                            <thead>
                                <tr class="text-gray-600">
                                    <th class="py-2">No</th>
                                    <th class="py-2">Bill ID</th>
                                    <th class="py-2">Supplier</th>
                                    <th class="py-2">Tanggal</th>
                                    <th class="py-2">Total Payment</th>
                                    <th class="py-2">Deadline</th>
                                    <th class="py-2">Arrears</th>
                                    <th class="py-2">Status</th>
                                    <th class="py-2">Update</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                <?php
                                $counter = 1;
                                while ($row = mysqli_fetch_assoc($result2)) {
                                    $status_class = '';
                                    switch ($row['status']) {
                                        case 'Paid':
                                            $status_class = 'text-green-500 bg-green-100';
                                            break;
                                        case 'Unpaid':
                                            $status_class = 'text-red-500 bg-red-100';
                                            break;
                                        case 'Partial':
                                            $status_class = 'text-orange-500 bg-orange-100';
                                            break;
                                    }
                                ?>
                                    <tr class="border-b">
                                        <td class="py-2"><?php echo $counter++; ?></td>
                                        <td class="py-2"><?php echo $row['nota_id']; ?></td>
                                        <td class="py-2"><?php echo $row['supplier_name']; ?></td>
                                        <td class="py-2"><?php echo $row['tanggal']; ?></td>
                                        <td class="py-2"><?php echo number_format($row['total_payment'], 2); ?></td>
                                        <td class="py-2"><?php echo $row['deadline']; ?></td>
                                        <td class="py-2"><?php echo number_format($row['debt_amount'], 2); ?></td>
                                        <td class="py-2"><span class="<?php echo $status_class; ?> px-2 py-1 rounded-full"><?php echo $row['status']; ?></span></td>
                                        <td class="py-2">
                                            <a href="purchase/detail.php?nota_id=<?php echo $row['nota_id']; ?>&supplier_name=<?php echo urlencode($row['supplier_name']); ?>">
                                                <i class="fas fa-pencil-alt text-gray-500"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>


                </main>
            </div>
        </div>
    </div>
</body>

</html>
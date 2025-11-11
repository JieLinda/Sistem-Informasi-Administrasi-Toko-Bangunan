<?php
// Memastikan file ini menerima parameter nota_id melalui URL
if (isset($_GET['nota_id']) && isset($_GET['nama_supplier'])) {
    $nota_id = htmlspecialchars($_GET['nota_id']);
    $nama_supplier = htmlspecialchars($_GET['nama_supplier']);
} else {
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
                or_.order_restock_delivered_qty AS 'Delivered',
                (or_.order_restock_price * or_.order_restock_qty) AS 'Total Payment',
                or_.order_restock_delivery_status AS 'Status',
                or_.order_restock_id AS 'order_restock_id'
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
if ($stmt === false || $stmt2 === false) {
    die('Error preparing statement: ' . $con->error);
}

if (!is_numeric($nota_id)) {
    die('Error: Invalid nota_id');
}

$stmt->bind_param("i", $nota_id);
$stmt2->bind_param("i", $nota_id);

$stmt->execute();
$result = $stmt->get_result();

$stmt2->execute();
$result2 = $stmt2->get_result();
$totalUnpaidRow = $result2->fetch_assoc();
$totalUnpaid = $totalUnpaidRow['total_hutang'] ?? 0;

if ($result->num_rows === 0) {
    die('Nota ID tidak ditemukan dalam database.');
}

$nota_data = $result->fetch_assoc();

$stmt->close();
$con->close();

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<script>
    $(document).ready(function () {
        // Fungsi tombol Update Delivered
        $(".update-delivered").on("click", function () {
            $("#shipmentTable1 tbody tr").each(function () {
                const deliveredCell = $(this).find("td:nth-child(5)"); // Kolom Delivered
                const inputField = $(this).find("input[type='number']"); // Input
                const currentDelivered = parseInt(deliveredCell.text()) || 0;
                const addedValue = parseInt(inputField.val()) || 0;

                // Validasi untuk memastikan nilai yang ditambahkan adalah bilangan bulat positif
                if (addedValue < 0) {
                    alert("Warning: Nilai yang ditambahkan tidak boleh negatif!");
                    return;
                }

                // Perbarui nilai kolom Delivered
                deliveredCell.text(currentDelivered + addedValue);

                // Reset input field
                inputField.val(0);
            });
        });

        // Fungsi tombol Return
        $(document).on("click", ".return-button", function () {
            const row = $(this).closest("tr");
            const deliveredCell = row.find("td:nth-child(5)"); const inputField = row.find("input[type='number']"); // Input
            const currentDelivered = parseInt($.trim(deliveredCell.text())) || 0;
            const subtractedValue = parseInt($.trim(inputField.val())) || 0;

            // Validasi untuk memastikan nilai yang dikembalikan tidak melebihi nilai Delivered
            if (subtractedValue < 0) {
                alert("Warning: Nilai yang dikembalikan tidak boleh negatif!");
                return;
            }

            if (currentDelivered >= subtractedValue) {
                // Perbarui nilai kolom Delivered
                deliveredCell.text(currentDelivered - subtractedValue);

                // Reset input field
                inputField.val(0);
            } else {
                alert("Warning: Stok tidak mencukupi untuk dikembalikan!");
            }
        });

        // Fungsi tombol Save Changes
        $(".save-changes").on("click", function () {
            let changes = [];
            const totalPaid = parseInt($("#paidAmount").val()) || 0;

            // Ambil perubahan dari tabel
            $("#shipmentTable1 tbody tr").each(function () {
                const row = $(this);
                const orderId = row.data("order-id"); // Ambil ID dari data attribute
                const deliveredQty = parseInt(row.find("td:nth-child(5)").text()) || 0;

                // Masukkan data perubahan ke array
                changes.push({ orderId, deliveredQty });
            });

            // Kirim perubahan ke server menggunakan AJAX
            $.ajax({
                url: "save_changes.php", // Endpoint untuk menyimpan perubahan
                method: "POST",
                data: {
                    changes: changes,
                    totalPaid: totalPaid,
                    notaId: "<?php echo $nota_id; ?>" // Ambil ID nota
                },
                success: function (response) {
                    alert(response.message); // Pesan sukses dari server
                    location.reload(); // Reload tabel
                },
                error: function () {
                    alert("Terjadi kesalahan saat menyimpan perubahan.");
                }
            });

            // Reset input setelah simpan
            $("#shipmentTable1 input[type='number']").val(0);
            $("#paidAmount").val("");
        });
    });
</script>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <?php include 'head.php'; ?>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex flex-col">
        <header class="bg-black text-white py-4 px-6 flex justify-between items-center w-full">
            <h1 class="text-xl font-semibold">Reseller Information System</h1>
        </header>
        <div class="flex">
            <div class="w-1/5 bg-white h-screen shadow-lg">
                <div class="flex flex-col items-center py-10">
                    <h2 class="text-lg font-semibold">Admin</h2>
                    <span class="text-sm text-green-500 bg-green-100 px-2 py-1 rounded-full">Administrator</span>
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
                            </a>
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
            <div class="w-4/5">
                <main class="p-6">
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h2 class="text-2xl font-semibold text-purple-600 mb-4">UD Makmur Abadi</h2>
                        <div class="flex items-center mb-6">
                            <p class="text-lg text-gray-600 mt-2">
                                Update Nota:
                                <span class="text-blue-600"><?php echo htmlspecialchars($nota_id); ?> [<?php echo htmlspecialchars($nama_supplier); ?>]</span>
                            </p>
                        </div>
                        <div class="mt-6 bg-white p-6 rounded-lg shadow-md">
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex gap-4">
                                    <button class="save-changes bg-purple-600 text-white px-4 py-2 rounded-full">Save changes</button>
                                    <button class="update-delivered bg-purple-600 text-white px-4 py-2 rounded-full">Update Delivered</button>
                                </div>
                                <div class="relative">
                                    <input class="border border-gray-300 rounded-full pl-10 pr-4 py-2" placeholder="Search for items" type="text"/>
                                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                                </div>
                            </div>
                        </div>
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
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $statusClass = ($row['Status'] == 'on process') ? 'text-orange-500 bg-orange-100' : 'text-green-500 bg-green-100';
                                ?>
                                    <tr class="border-t" data-order-id="<?php echo $row['order_restock_id']; ?>">
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
                                            <input class="border border-gray-300 rounded-full w-16 text-center" type="number" min="0" step="1" value="0" />
                                        </td>
                                        <td class="py-2">
                                            <button class="return-button bg-purple-600 text-white px-2 py-1 rounded-full">Return</button>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-lg text-gray-600 mt-2">
                        Total Unpaid: <span id="totalUnpaid"><?php echo number_format($totalUnpaid, 2); ?></span>
                    </p>
                    <div class="mt-6 flex justify-end">
                        <input id="paidAmount" class="border border-gray-200 rounded-full pl-10 py-1" placeholder="Paid amount" type="text" />
                    </div>
                </main>
            </div>
        </div>
    </div>
</body>
</html>

<?php
require_once('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $changes = json_decode($_POST['changes'], true);
    $totalPaid = $_POST['totalPaid'];
    $notaId = $_POST['notaId'];

    // Update Order_Restock and Produk
    foreach ($changes as $change) {}
        $orderId = $change['orderId'];
        $deliveredQty = $change['deliveredQty'];

        // Update Order_Restock
        $updateOrder = $con->prepare("UPDATE Order_Restock SET order_restock_delivered_qty = ? WHERE order_restock_id = ?");
        $updateOrder->bind_param("ii", $deliveredQty, $orderId);
        $updateOrder->execute();

        // Update Produk
        $updateProduct = $con->prepare("UPDATE Produk SET stok = stok + ? WHERE produk_id = (SELECT produk_id FROM Order_Restock WHERE order_restock_id = ?)");
        $updateProduct->bind_param("ii", $deliveredQty, $orderId);
        $updateProduct->execute();
    }

    // Update Hutang
    if ($totalPaid > 0) {
        $queryHutang = "SELECT hutang_total FROM hutang WHERE nota_id = ? ORDER BY hutang_total ASC";
        $stmtHutang = $con->prepare($queryHutang);
        $stmtHutang->bind_param("i", $notaId);
        $stmtHutang->execute();
        $resultHutang = $stmtHutang->get_result();
        $remainingPayment = $totalPaid;

        while ($row = $resultHutang->fetch_assoc()) {
            $hutangTotal = $row['hutang_total'];

            if ($remainingPayment >= $hutangTotal) {
                // Jika sisa pembayaran lebih besar atau sama dengan hutang_total, hapus baris hutang
                $deleteHutang = $con->prepare("DELETE FROM hutang WHERE nota_id = ? AND hutang_total = ?");
                $deleteHutang->bind_param("id", $notaId, $hutangTotal);
                $deleteHutang->execute();
                $remainingPayment -= $hutangTotal;
            } else {
                // Jika sisa pembayaran kurang dari hutang_total, update nilai hutang
                $newHutangTotal = $hutangTotal - $remainingPayment;
                $updateHutang = $con->prepare("UPDATE hutang SET hutang_total = ? WHERE nota_id = ? AND hutang_total = ?");
                $updateHutang->bind_param("idi", $newHutangTotal, $notaId, $hutangTotal);
                $updateHutang->execute();
                break; // Keluar dari loop setelah mengupdate
            }
        }
    }

    // Menghitung total unpaid yang baru
    $queryTotalUnpaid = "SELECT SUM(hutang_total) AS total_hutang FROM hutang WHERE nota_id = ?";
    $stmtTotalUnpaid = $con->prepare($queryTotalUnpaid);
    $stmtTotalUnpaid->bind_param("i", $notaId);
    $stmtTotalUnpaid->execute();
    $resultTotalUnpaid = $stmtTotalUnpaid->get_result();
    $totalUnpaidRow = $resultTotalUnpaid->fetch_assoc();
    $newTotalUnpaid = $totalUnpaidRow['total_hutang'] ?? 0;

    echo json_encode(['message' => 'Changes saved successfully.', 'newTotalUnpaid' => number_format($newTotalUnpaid, 2)]);
$con->close();
?>

<?php

// require '../authen.php';
require_once '../logic/db.php';


session_start();
if ( !isset($_SESSION['username']) ){
    header("Location: login.php");
    exit();
}

// $username = $_SESSION['username'];
define("VALID_ACCESS", 1);

require_once '../logic/queries.php';

// Memastikan file ini menerima parameter nota_id melalui URL
if (!isset($_GET['nota_id']) or !isset($_GET['supplier_name'])) {
    die('Error: nota_id atau nama_supplier tidak ditemukan.');
}

$nota_id = htmlspecialchars($_GET['nota_id']);
$nama_supplier = htmlspecialchars($_GET['supplier_name']);

// Handle update delivered
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    $order_restocks = get_order_restock_by_nota_id($conn, $nota_id);
    
    // Returns handler.
    if ($data['returns'] !== null) {
        $order_restock_id = $data['returns']['order_restock_id'];
        $qty = $data['returns']['qty'];
        
        $order_restock = array_filter($order_restocks, function($order_restock) use ($order_restock_id) {
            return $order_restock['order_restock_id'] == $order_restock_id;
        });
        
        if (count($order_restock) == 0) {
            http_response_code(400);
            echo json_encode(['message' => 'Order restock not found']);
            exit;
        }
        
        $order_restock = $order_restock[0];
        
        // Reject invalid qty.
        if ($qty > $order_restock['order_restock_delivered_qty']) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid value']);
            exit;
        }
        
        // Reduce delivered items
        $query = "UPDATE Order_Restock 
                  SET order_restock_delivered_qty = order_restock_delivered_qty - ? 
                  WHERE order_restock_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$qty, $order_restock_id]);
        
        // Forecast the new delivered reduction.
        $new_delivered = $order_restock['order_restock_delivered_qty'] - $qty;
        
        // If it is changing the current state (not all qty is delivered now)
        // then update to pending.
        if ($new_delivered < $order_restock['order_restock_qty']) {
            $query = "UPDATE Order_Restock 
                SET order_restock_delivery_status = 'Pending' 
                WHERE order_restock_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$order_restock_id]);
        }
        
        http_response_code(200);
        echo json_encode(['message' => 'Update return successful']);
        exit;
    }
    
    // Updates handler.
    foreach ($data['updates'] as $index => $item) {
        $order_restock_index = array_search($item['id'], array_column($order_restocks, 'order_restock_id'));
        $data['updates'][$index]['is_fully_delivered'] = false;
        if ($item['value'] > $order_restocks[$order_restock_index]['order_restock_qty']) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid value']);
            exit;
        }
        if ($item['value'] == $order_restocks[$order_restock_index]['order_restock_qty']) {
            $data['updates'][$index]['is_fully_delivered'] = true;
        }
    }
    update_delivered_by_order_restock_id($conn, $data['updates']);
    update_hutang_by_nota_id($conn, $nota_id, floatval($data['paidAmount']));
    
    http_response_code(200);
    echo json_encode(['message' => 'Update delivered successfully']);
    exit;
}

$order_restock = get_order_restock_by_nota_id($conn, $nota_id);

$querySUM = "SELECT nota_id, SUM(debt_amount) AS total_hutang
            FROM hutang
            WHERE nota_id = ?
            GROUP BY nota_id;";

$stmt2 = $conn->prepare($querySUM);
if ($stmt2 === false) {
    die('Error preparing statement: ' . $conn->error);
}

// Bind the parameter dan validasi nilai
if (!is_numeric($nota_id)) {
    die('Error: Invalid nota_id');
}

$stmt2->bind_param("i", $nota_id);

$stmt2->execute();
$result2 = $stmt2->get_result();
$totalUnpaidRow = $result2->fetch_assoc();
$totalUnpaid = $totalUnpaidRow['total_hutang'] ?? 0;

?>
<!-- Script -->
<script>
    $(document).ready(function() {
        // Fungsi tombol Update Delivered
        $(".update-delivered").on("click", function() {
            $("#shipmentTable1 tbody tr").each(function() {
                const deliveredCell = $(this).find("td:nth-child(5)"); // Kolom Delivered
                const inputField = $(this).find("input[type='number']"); // Input
                const currentDelivered = parseInt(deliveredCell.text()) || 0;
                const addedValue = parseInt(inputField.val()) || 0;

                // Perbarui nilai kolom Delivered
                deliveredCell.text(currentDelivered + addedValue);

                // Reset input field
                inputField.val(0);
            });
        });

        // Fungsi tombol Return
        $(document).on("click", ".return-button", function() {
            const row = $(this).closest("tr");
            const deliveredCell = row.find("td:nth-child(5)"); // Kolom Delivered
            const inputField = row.find("input[type='number']"); // Input
            const currentDelivered = parseInt($.trim(deliveredCell.text())) || 0;
            const subtractedValue = parseInt($.trim(inputField.val())) || 0;

            if (currentDelivered >= subtractedValue) {
                // Perbarui nilai kolom Delivered
                deliveredCell.text(currentDelivered - subtractedValue);

                // Reset input field
                inputField.val(0);
                closeReturnModal();
            } else {
                alert("Warning: Stok tidak mencukupi untuk dikembalikan!");
            }
        });
        

        // Fungsi tombol Save Changes
        $(".save-changes").on("click", function() {
            let changes = [];
            const totalPaid = parseInt($("#paidAmount").val()) || 0;

            // Ambil perubahan dari tabel
            $("#shipmentTable1 tbody tr").each(function() {
                const row = $(this);
                const orderId = row.data("order-id"); // Ambil ID dari data attribute
                const deliveredQty = parseInt(row.find("td:nth-child(5)").text()) || 0;

                // Masukkan data perubahan ke array
                changes.push({
                    orderId,
                    deliveredQty
                });
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
                complete: function(xhr) {
                    if (xhr.status != 200) {
                        alert(JSON.parse(xhr.responseText).message);
                    }
                    location.reload();
                },
                error: function(err) {
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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src='tailwind.config.js'></script>
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
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                <?php
                                $i = 1;
                                foreach ($order_restock as $row):
                                    $statusClass = ($row['order_restock_delivery_status'] == 'on process') ? 'text-orange-500 bg-orange-100' : 'text-green-500 bg-green-100';
                                ?>
                                    <tr class="border-t" data-order-id="<?= $row['order_restock_id']; ?>">
                                        <td class="py-2"><?= $i++; ?></td>
                                        <td class="py-2"><?= $row['product_name']; ?></td>
                                        <td class="py-2"><?= number_format($row['order_restock_price'], 2, '.', ','); ?></td>
                                        <td class="py-2"><?= $row['order_restock_qty']; ?></td>
                                        <td class="py-2">
                                            <input data-id="<?= $row['order_restock_id']; ?>" type="number" min="0" max="<?= $row['order_restock_qty']; ?>" class="update-delivered border px-2 py-1 rounded w-16 text-center" value="<?= $row['order_restock_delivered_qty'] ?? 0 ?>">
                                        </td>
                                        <td class="py-2"><?= number_format($row['total_payment'], 2, '.', ','); ?></td>
                                        <td class="py-2">
                                            <span class="px-2 py-1 rounded-full <?= $statusClass; ?>"><?= ucfirst($row['order_restock_delivery_status']); ?></span>
                                        </td>
                                        <td class="py-2">
                                            <button class="return-button bg-purple-600 text-white px-2 py-1 rounded-full" onclick="openReturnModal('<?= $row['order_restock_id']; ?>')">Return</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="flex text-lg text-gray-600 mt-2 items-center space-x-4">
                            <span>
                                Total Unpaid: <?= number_format($totalUnpaid, 2); ?>
                            </span>
                            <input id="paidAmount" class="border border-gray-200 rounded pl-10 py-1" placeholder="Paid amount" type="text" />
                        </div>
                        <div class="mt-4 flex gap-4 justify-end">
                            <button class="bg-purple-600 text-white px-4 py-2 rounded-full" onclick="saveChanges()">
                                Save changes
                            </button>
                        </div>
                    </div>
                    

                    <div class="mt-6 flex justify-end">
                    </div>
            </div>

        </div>
    </div>

    <!-- Modal untuk input teks -->
    <div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-semibold mb-4">Update Quantity</h3>
            <input type="text" data-id="" id="inputText" class="border border-gray-300 rounded w-full px-2 py-1 mb-4" placeholder="Enter details...">
            <div class="flex justify-end">
                <button id="saveButton" class="bg-blue-600 text-white px-4 py-2 rounded mr-2" onclick="saveModal()">Save</button>
                <button id="cancelButton" class="bg-gray-400 text-white px-4 py-2 rounded" onclick="closeReturnModal()">Cancel</button>
                
            </div>
        </div>
    </div>


</body>
<script>
    function openReturnModal(id) {
        $("#modal").removeClass("hidden");
        $("#inputText").attr('data-id', id);
    }
    function closeReturnModal() {
        $("#modal").addClass("hidden");
        $("#inputText").attr('data-id', '');
    }

    function saveChanges() {
        const updates = [];
        
        $('.update-delivered').each(function() {
            const id = $(this).data('id');
            const value = $(this).val();
            updates.push({ id, value });
        });
        
        $.ajax({
            url: '',
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({ 
                returns: null,
                updates: updates, 
                paidAmount: $('#paidAmount').val()
            }),
            complete: function(xhr) {
                alert(JSON.parse(xhr.responseText).message);
                location.reload();
            }
        });
    }
    
    function saveModal() {
        const $inputText = $('#inputText');
        const id = $inputText.attr('data-id');
        const qty = $inputText.val();
        
        $.ajax({
            url: '',
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({ 
                returns: {
                    order_restock_id: id,
                    qty: qty,
                },
                updates: null, 
                paidAmount: null,
            }),
            complete: function(xhr) {
                alert(JSON.parse(xhr.responseText).message);
                location.reload();
            }
        });
    }

</script>

</html>
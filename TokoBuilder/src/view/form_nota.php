<?php

// require '../authen.php';
require_once '../logic/db.php';

session_start();
if ( !isset($_SESSION['username']) ){
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

define("VALID_ACCESS", 1);

require_once '../logic/queries.php';

$products = $conn->query("SELECT * FROM Produk")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = $_POST['tanggal'];
    $supplier_id = $_POST['supplier'];
    $save_method = $_POST['save_method'];
    $detail_pembelian = $_POST['detail_pembelian'] ?? null;

    $total = 0;
    foreach ($_POST['products'] as $product) {
        $total += $product['qty'] * $product['price'];
    }

   
    
    $stmt = $conn->prepare("INSERT INTO Nota_Pembelian (total, save_method, detail_pembelian, tanggal, supplier_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('dsssi', $total, $save_method, $detail_pembelian, $tanggal, $supplier_id);
    $stmt->execute();
    $nota_id = $conn->insert_id;


    // Insert into Hutang table if 'Credit' is selected
    if ($save_method === 'credit') {
        $hutang_deadline = $_POST['hutang_deadline'];
        $stmt = $conn->prepare("INSERT INTO Hutang (deadline, debt_amount, nota_id) VALUES (?, ?, ?)");
        $stmt->bind_param('ssi', $hutang_deadline, $total, $nota_id);
        $stmt->execute();
    }

    // Insert into Order_Restock table for each product
    foreach ($_POST['products'] as $product) {
        if ($product['qty'] > 0) {
            $stmt = $conn->prepare("INSERT INTO Order_Restock (order_restock_qty, order_restock_price, order_restock_delivery_status, nota_id, produk_id) VALUES (?, ?, 'pending', ?, ?)");
            $stmt->bind_param('diii', $product['qty'], $product['price'], $nota_id, $product['produk_id']);
            $stmt->execute();
        }
    }

    // Redirect to a confirmation page or display success
    header("Location: restock.php");
    exit;
} else {
    $suppliers = get_all_suppliers($conn);
}
?>

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

        .product-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin: 8px;
            text-align: center;
            background-color: white;
            transition: transform 0.2s;
        }

        .product-card:hover {
            transform: scale(1.05);
        }

        .integer-picker {
            width: 60px;
            text-align: center;
        }

        .grid-1-1 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .product-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="flex flex-col">
        <!-- Full Width Header -->
        <header class="bg-black text-white py-4 px-6 flex justify-between items-center w-full">
            <h1 class="text-xl font-semibold">Reseller Information System</h1>
            
        </header>
        <div class="flex">
            
            <!-- Main Content -->
            <div class="w-4/5 p-6">
                <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
                    <h2 class="text-2xl font-semibold text-purple-600 mb-4">UD Makmur Abadi</h2>
                    <div class="flex items-center mb-6">
                        <h3 class="text-xl font-semibold text-gray-700">Tambah Data Sales</h3>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-lg font-semibold">Detail Transaksi</h3>
                    <form action="form_nota.php" method="POST" onsubmit="onSubmitForm()">
                        <div class="grid-1-1">
                            <div>
                                <label for="date-picker" class="block">Tanggal:</label>
                                <input type="date" id="date-picker" name="tanggal" class="border rounded p-2 w-full" required />
                            </div>
                            <div>
                                <label for="supplier-dropdown" class="block">Supplier:</label>
                                <select id="supplier-dropdown" name="supplier" class="border rounded p-2 w-full" required>
                                    <option value="">Pilih Supplier</option>
                                    <?php foreach ($suppliers as $supplier): ?>
                                        <option value="<?= $supplier['supplier_id'] ?>">
                                            <?= $supplier['supplier_name'] ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="grid-1-1 mt-4">
                            <div>
                                <label for="save-method" class="block">Save Method:</label>
                                <select id="save-method" name="save_method" class="border rounded p-2 w-full" required>
                                    <option value="cash">Cash</option>
                                    <option value="credit">Credit</option>
                                </select>
                            </div>
                            <div>
                                <label for="date-picker" class="block">Deadline pembayaran credit:</label>
                                <input type="date" id="date-picker" name="hutang_deadline" class="border rounded p-2 w-full" required />
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="button" class="bg-purple-500 text-white rounded p-3 mt-4 product-modal" onclick="showProductsModal()">Tambah Produk</button>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-300">
                                    <!-- Table Header -->
                                    <thead>
                                        <tr class="bg-gray-200">
                                            <th class="py-2 px-4 border-b text-left">No</th>
                                            <th class="py-2 px-4 border-b text-left">Nama Produk</th>
                                            <th class="py-2 px-4 border-b text-left">Stok</th>
                                            <th class="py-2 px-4 border-b text-left">Harga Jual</th>
                                        </tr>
                                    </thead>
                                    <tbody id="products-table"></tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                <label for="detail-pembelian" class="block">Detail Pembelian:</label>
                                <textarea id="detail-pembelian" name="detail_pembelian" class="border rounded p-2 w-full" rows="3"></textarea>
                            </div>
                            <div>
                                <button type="submit" class="bg-purple-500 text-white rounded p-3 mt-4">Simpan Transaksi</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-gray-100 rounded-lg border shadow-lg p-6 hidden absolute z-50 top-[50%] left-[50%] -translate-x-1/2 -translate-y-1/2 h-[80vh] flex flex-col" id="product-modal">
        <h2 class="text-2xl font-bold">Produk</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 overflow-y-auto overflow-x-hidden h-full" id="product-grid">
            <!-- Example Product Card -->
            <?php foreach ($products as $product) : ?>
                <div class="product-card">
                    <!-- <img src="https://via.placeholder.com/150 " alt="Product Image" class="product-image" /> -->
                    <h4 class="font-semibold nama-produk"><?= $product['product_name']; ?></h4>
                    <input type="hidden" name="produk_id" value="<?= $product['product_id'] ?>">
                    <input type="number" name="qty" class="integer-picker" value="0" min="0" placeholder="QTY" />
                    <input type="hidden" name="harga_asli" value="<?= $product['product_price'] ?>">
                    <input type="number" name="harga" class="border rounded p-2 w-full mt-2 price" oninput="calculateTotal(this)" placeholder="Harga" />
                    <span class="harga-error text-red-500"></span>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="flex flex-row justify-end space-x-3">
            <button class="bg-purple-500 text-white rounded px-5 py-3 mt-4" onclick="saveSelectedProducts()">Simpan</button>
            <button class="bg-red-500 text-white rounded p-3 mt-4" onclick="closeProductModal()">Tutup</button>
        </div>
    </div>

</body>
<script>
    function showProductsModal() {
        console.log("masuk")
        $('#product-modal').removeClass('hidden');
    }

    function closeProductModal() {
        $('#product-modal').addClass('hidden');
    }

    function saveSelectedProducts() {
        closeProductModal();
        
        const selectedItems = $('#product-grid > .product-card')
            .filter(function(index, element) {
                return $(element).find('input[name="qty"]').val() > 0;
            })
            .map(function(index, element) {
                return {
                    produk_id: $(element).find('input[name="produk_id"]').val(),
                    nama_produk: $(element).find('.nama-produk').text(),
                    qty: $(element).find('input[name="qty"]').val(),
                    harga: $(element).find('input[name="harga"]').val(),
                };
            })
            .get();

        $('#products-table').html('');
        console.log(selectedItems);
        selectedItems.forEach(function(item, index) {
            $('#products-table').append(`
                <tr>
                    <td class="py-2 px-4 border-b text-left" data-produk-id="${item.produk_id}">${index + 1}</td>
                    <td class="py-2 px-4 border-b text-left">${item.nama_produk}</td>
                    <td class="py-2 px-4 border-b text-left">${item.qty}</td>
                    <td class="py-2 px-4 border-b text-left">${item.harga}</td>
                </tr>
            `);
        });
    }
    
    function onSubmitForm() {
        // Get all rows from the products table
        const products = [];
        $('#products-table tr').each(function() {
            const cells = $(this).find('td');
            if (cells.length) {
                products.push({
                    produk_id: $(this).find('td[data-produk-id]').attr('data-produk-id'),
                    qty: parseFloat(cells.eq(2).text()),
                    price: parseFloat(cells.eq(3).text())
                });
            }
        });
        
        // Create hidden inputs for each product
        products.forEach((product, index) => {
            $('form').append(`
                <input type="hidden" name="products[${index}][produk_id]" value="${product.produk_id}">
                <input type="hidden" name="products[${index}][qty]" value="${product.qty}">
                <input type="hidden" name="products[${index}][price]" value="${product.price}">
            `);
        });

        return true; // Prevent default form submission
    }
    
    function calculateTotal(element) {
        const hargaAsli = parseInt($(element).parent('.product-card').find('input[name="harga_asli"]').val());
        const harga = parseInt($(element).val());
        console.log("HargaAsli", hargaAsli, "Harga", harga);
        if (harga > hargaAsli) {
            $(element).parent('.product-card').find('.harga-error').text('Harga tidak boleh lebih besar dari harga asli: ' + harga + " vs " + hargaAsli);
        } else {
            $(element).parent('.product-card').find('.harga-error').text('');
        }
    }
</script>

</html>
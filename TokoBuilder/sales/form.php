<?php
require '../authen.php';

if ( !isset($_SESSION['username']) ){
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

define("VALID_ACCESS", 1);

require_once '../db.php';
require_once '../_repositories/queries.php';

$products = get_all_products($con,$username);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = $_POST['tanggal'];
    $nama_pembeli = $_POST['nama_pembeli'];
    
    try {
        create_sales_record($con, $tanggal, $_POST['products'], $username);
        header("Location: " . base_url() . "/sales/index.php");
        exit;
    } catch (Exception $e) {
        die("Error creating sales record: " . $e->getMessage());
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">

<head>
    <?php include '../head.php'; ?>
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
            <!-- Sidebar -->
            <div class="w-1/5 bg-white h-screen shadow-lg">
                <div class="flex flex-col items-center py-10">
                    
                    <h2 class="text-lg font-semibold">Admin</h2>
                    <span class="text-sm text-green-500 bg-green-100 px-2 py-1 rounded-full">Administrator</span>
                </div>
                <?php require_once '../includes/sidebar.php' ?>
            </div>
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
                    <form action="<?= base_url() ?>/sales/form.php" method="POST" onsubmit="onSubmitForm()">
                        <div class="grid-1-1">
                            <div>
                                <label for="date-picker" class="block">Tanggal:</label>
                                <input type="date" id="date-picker" name="tanggal" class="border rounded p-2 w-full" required />
                            </div>
                            <div>
                                <label for="supplier-dropdown" class="block">Nama Pembeli:</label>
                                <input type="text" id="nama_pembeli" name="nama_pembeli" class="border rounded p-2 w-full" required />
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="button" class="bg-purple-500 text-white rounded p-3 mt-4" onclick="showProductsModal()">Tambah Produk</button>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-300">
                                    <!-- Table Header -->
                                    <thead>
                                        <tr class="bg-gray-200">
                                            <th class="py-2 px-4 border-b text-left">No</th>
                                            <th class="py-2 px-4 border-b text-left">Nama Produk</th>
                                            <th class="py-2 px-4 border-b text-left">Qty</th>
                                            <th class="py-2 px-4 border-b text-left">Harga</th>
                                            <th class="py-2 px-4 border-b text-left">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="products-table"></tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="py-2 px-4 border-b text-left">Total</td>
                                            <td id="total-price" class="py-2 px-4 border-b text-left"></td>
                                        </tr>
                                    </tfoot>
                                </table>
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
            <?php foreach ($products as $product) : ?>
                <div class="product-card">
                    <!-- <img src="https://via.placeholder.com/150 " alt="Product Image" class="product-image" /> -->
                    <h4 class="font-semibold nama-produk"><?= $product['nama_produk']; ?></h4>
                    <input type="hidden" name="produk_id" value="<?= $product['produk_id'] ?>">
                    <p>Stok: <?= $product['stok'] ?></p>
                    <p>Harga: <span class="harga-produk text-green-500"><?= $product['harga_jual'] ?></span></p>
                    <input type="number" name="qty" class="integer-picker" value="0" min="0" placeholder="QTY" />
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
                const qty = parseInt($(element).find('input[name="qty"]').val());
                const harga = parseInt($(element).find('.harga-produk').text());
                return {
                    produk_id: $(element).find('input[name="produk_id"]').val(),
                    nama_produk: $(element).find('.nama-produk').text(),
                    qty: qty,
                    harga: harga,
                    subtotal: qty * harga
                };
            })
            .get();

        $('#products-table').html('');
        selectedItems.forEach(function(item, index) {
            $('#products-table').append(`
                <tr>
                    <td class="py-2 px-4 border-b text-left" data-produk-id="${item.produk_id}">${index + 1}</td>
                    <td class="py-2 px-4 border-b text-left">${item.nama_produk}</td>
                    <td class="py-2 px-4 border-b text-left">${item.qty}</td>
                    <td class="py-2 px-4 border-b text-left">Rp${Number(item.harga).toLocaleString('id-ID')}</td>
                    <td class="py-2 px-4 border-b text-left">Rp${Number(item.subtotal).toLocaleString('id-ID')}</td>
                </tr>
            `);
        });
        
        $('#total-price').text('Rp' + selectedItems.reduce((total, item) => total + item.subtotal, 0).toLocaleString('id-ID'));
    }

    function onSubmitForm() {
        const products = [];
        $('#products-table tr').each(function() {
            const cells = $(this).find('td');
            if (cells.length) {
                products.push({
                    produk_id: $(this).find('td[data-produk-id]').attr('data-produk-id'),
                    qty: parseFloat(cells.eq(2).text()),
                    price: parseFloat(cells.eq(3).text().replace('Rp', '').replace(/\./g, ''))
                });
            }
        });
        
        products.forEach((product, index) => {
            $('form').append(`
                <input type="hidden" name="products[${index}][produk_id]" value="${product.produk_id}">
                <input type="hidden" name="products[${index}][qty]" value="${product.qty}">
            `);
        });

        return true;
    }
</script>

</html>
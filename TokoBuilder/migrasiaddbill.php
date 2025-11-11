
<!DOCTYPE HTML>
<html lang="en">
<head>
    <?php include 'head.php'; ?>
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
            <h1 class="text-xl font-semibold">
                Reseller Information System
            </h1>
            <img alt="User  profile picture" class="rounded-full" height="40" src="https://storage.googleapis.com/a1aa/image/6SeyLSBlUxTLfEygcTCmONTntQMZamEDRazHWvz5G280KHzTA.jpg" width="40"/>
        </header>
        <div class="flex">
            <!-- Sidebar -->
            <div class="w-1/5 bg-white h-screen shadow-lg">
                <div class="flex flex-col items-center py-10">
                    <img alt="Admin profile picture" class="rounded-full mb-4" height="100" src="https://storage.googleapis.com/a1aa/image/1p3FXQ32tVbvH1aNReA5de5AUWaoXncsKXgGzy21qyy1KHzTA.jpg" width="100"/>
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
                        <li class="flex items-center px-10 py-3 text-gray-600 text-gray-600 bg-gray-100">
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
                        <h2 class="text-2xl font-semibold text-purple-600 mb-4">UD Makmur Abadi</h2>
                        <div class="flex items-center mb-6">
                            <h3 class="text-xl font-semibold text-gray-700">Data Sales</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Example Product Card -->
                            <div class="product-card">
                                <img src="https://via.placeholder.com/150 " alt="Product Image" class="product-image" />
                                <h4 class="font-semibold">Nama Produk 1</h4>
                                <input type="number" class="integer-picker" value="0" min="0" placeholder="QTY" />
                                <input type="text" class="border rounded p-2 w-full mt-2" placeholder="Harga" />
                            </div>
                            <div class="product-card">
                                <img src="https://via.placeholder.com/150" alt="Product Image" class="product-image" />
                                <h4 class="font-semibold">Nama Produk 2</h4>
                                <input type="number" class="integer-picker" value="0" min="0" placeholder="QTY" />
                                <input type="text" class="border rounded p-2 w-full mt-2" placeholder="Harga" />
                            </div>
                            <div class="product-card">
                                <img src="https://via.placeholder.com/150" alt="Product Image" class="product-image" />
                                <h4 class="font-semibold">Nama Produk 3</h4>
                                <input type="number" class="integer-picker" value="0" min="0" placeholder="QTY" />
                                <input type="text" class="border rounded p-2 w-full mt-2" placeholder="Harga" />
                            </div>
                            <!-- Add more product cards as needed -->
                        </div>
                    </div>
                </main>
                <div class="mt-6">
                    <h3 class="text-lg font-semibold">Detail Transaksi</h3>
                    <form>
                        <div class="grid-1-1">
                            <div>
                                <label for="date-picker" class="block">Tanggal:</label>
                                <input type="date" id="date-picker" class="border rounded p-2 w-full" required />
                            </div>
                            <div>
                                <label for="supplier-dropdown" class="block">Supplier:</label>
                                <select id="supplier-dropdown" class="border rounded p-2 w-full" required>
                                    <option value="">Pilih Supplier</option>
                                    <option value="Supplier 1">Supplier 1</option>
                                    <option value="Supplier 2">Supplier 2</option>
                                    <option value="Supplier 3">Supplier 3</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid-1-1 mt-4">
                            <div>
                                <label for="save-method" class="block">Save Method:</label>
                                <select id="save-method" class="border rounded p-2 w-full" required>
                                    <option value="cash">Cash</option>
                                    <option value="credit">Credit</option>
                                </select>
                            </div>
                            <div>
                                <label for="date-picker" class="block">Deadline pembayaran credit:</label>
                                <input type="date" id="date-picker" class="border rounded p-2 w-full" required />
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="detail-pembelian" class="block">Detail Pembelian:</label>
                            <textarea id="detail-pembelian" class="border rounded p-2 w-full" rows="3"></textarea>
                        </div>
                        <button type="submit" class="bg-purple-600 px-4 py-2 rounded-lg mt-4 text-white">Simpan Transaksi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
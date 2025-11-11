<?php 
$conn = new mysqli("localhost", "root", "", "toko4");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Form</title>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-lg">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Form Catatan Penjualan</h1>

        <div class="mb-6">
            <form action="cetak.php" method="post" class="flex flex-col items-center">
                <button 
                    type="submit" 
                    class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 transition">Print One Day
                </button>
            </form>
        </div>

        <div class="mb-6">
            <form action="cetakHarian.php" method="post">
                <label for="harian" class="block text-gray-700 font-medium mb-2">Transaction Date:</label>
                <input type="date" id="harian" name="harian" class="w-full p-2 border border-gray-300 rounded mb-4 focus:ring-2 focus:ring-blue-400" required>
                <button type="submit" name="ha" class="w-full bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition">Daily</button>
            </form>
        </div>

        <div>
            <form action="cetakJarak.php" method="post">
                <label for="tglMulai" class="block text-gray-700 font-medium mb-2">Tanggal Mulai:</label>
                <input type="date" name="tglMulai" class="w-full p-2 border border-gray-300 rounded mb-4 focus:ring-2 focus:ring-blue-400" required>
                
                <label for="tglAkhir" class="block text-gray-700 font-medium mb-2">Tanggal Selesai:</label>
                <input type="date" name="tglAkhir" class="w-full p-2 border border-gray-300 rounded mb-4 focus:ring-2 focus:ring-blue-400" required>
                <button type="submit" name="jarak" class="w-full bg-purple-500 text-white font-bold py-2 px-4 rounded hover:bg-purple-600 transition">Jarak</button>
            </form>
        </div>
    </div>


    
</body>
</html>
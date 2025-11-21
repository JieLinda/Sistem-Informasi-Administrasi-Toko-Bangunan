<?php 

$conn = new mysqli("localhost", "root", "", "adsi1");

// if(isset($_POST['create'])){
//     if(create($_POST) > 0){
//         echo "<script>alert('Berhasil ditambahkan')</script>";
//     }else{
//         echo "<script>alert('Gagal ditambahkan')</script>";
//     }
// }
 
// function create($data){
//     global $conn;
//     $nama = $data['nama'];
//     $stok = $data['stok'];
//     $transaction_date = $data['transaction_date'];

//     $query = mysqli_query($conn, "INSERT INTO penjualan VALUES('','$nama',$stok,'$transaction_date')");
//     return mysqli_affected_rows($conn);
// }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Form</title>
</head>
    <!-- <h2>Create Catatan Penjualan</h2>
    <form action="" method="post">
        <label for="name">Nama Produk:</label>
        <input type="text" id="nama" name="nama" required><br>

        <label for="stok">Stok:</label>
        <input type="number" id="stok" name="stok" required><br>

        <label for="">Transaction Date:</label>
        <input type="date" id="transaction_date" name="transaction_date" required><br>
        <button type="submit" name="create">Submit</button>
    </form> -->
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-lg">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Form Catatan Penjualan</h1>

        <div class="mb-6">
            <form action="cetak.php" method="post" class="flex flex-col items-center">
                <button 
                    type="submit" 
                    class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 transition">Print All Day
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
                <button type="submit" name="jarak" class="w-full bg-purple-500 text-white font-bold py-2 px-4 rounded hover:bg-purple-600 transition mb-4">Jarak</button>
            </form>
            
            <button class="w-full bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-600 transition "><a href="sales.php">Kembali</a></button>
        </div>
    </div>

    
    <!-- <form action="cetak.php" method="post">
        <button type="submit">Print All Day</button>
    </form>

    <form action="cetakHarian.php" method="post">
        <label for="">Transaction Date:</label>
        <input type="date" id="harian" name="harian" required><br>
        <button type="submit" name="ha">harian</button>
    </form>

    <form action="cetakJarak.php" method="post">
        <label for="">Tanggal Mulai:</label>
        <input type="date" id="tglMulai" name="tglMulai" required><br>
        <label for="">Tanggal Selesai:</label>
        <input type="date" id="tglAkhir" name="tglAkhir" required><br>
        <button type="submit" name="jarak">Jarak</button>
    </form> -->

    
</body>
</html>
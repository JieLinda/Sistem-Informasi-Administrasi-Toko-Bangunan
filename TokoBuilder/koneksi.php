<?php
    // Konfigurasi database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "tokobangunan";

    try {
        // Membuat koneksi menggunakan PDO
        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        // Mengatur mode error PDO agar melempar exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Koneksi berhasil"; // Opsional: Hapus atau ganti untuk produksi
    } catch (PDOException $e) {
        echo "Koneksi gagal: " . $e->getMessage();
    }
?>

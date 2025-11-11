<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

try {
    $conn = new PDO("mysql:host=localhost;dbname=toko4", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

function jarak($data){
    global $conn;
    $tglMulai = $data['tglMulai'];
    $tglAkhir = $data['tglAkhir'];

    $query = "SELECT * FROM catatan_penjualan WHERE transaction_date BETWEEN ? and ?";
    $stmt = $conn->prepare($query);
    $stmt->bindParam('ss', $tglMulai, $tglAkhir);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_POST['jarak'])) {
    $tglMulai = $_POST['tglMulai'];
    $tglAkhir = $_POST['tglAkhir'];
    $rows = jarak(['tglMulai' => $tglMulai, 'tglAkhir' => $tglAkhir]);

    if (empty($rows)) {
        echo "Tidak ada data untuk rentang tanggal $tglMulai hingga $tglAkhir.";
        exit;
    }

    $html = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Invoice</title>
        <style>
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid #000;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
            }
        </style>
    </head>
    <body>
        <h2>Invoice Penjualan - ' . htmlspecialchars($tglMulai) . ' hingga ' . htmlspecialchars($tglAkhir) . '</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Stok</th>
                    <th>Tanggal Transaksi</th>
                </tr>
            </thead>
            <tbody>
    ';

    foreach ($rows as $row) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($row['produk']) . '</td>
                    <td>' . number_format($row['jumlah_terjual']) . '</td>
                    <td>' . htmlspecialchars($row['transaction_date']) . '</td>
                </tr>';
    }

    $html .= '</tbody>
        </table>
    </body>
    </html>';

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $dompdf->stream("invoice.pdf", ["Attachment" => 0]);
}
?>

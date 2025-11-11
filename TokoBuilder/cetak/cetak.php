<?php
require 'vendor/autoload.php';

use Dompdf\Dompdf;

$conn = new mysqli("localhost", "root", "", "toko4");

$sql = "SELECT * FROM catatan_penjualan";
$result = $conn->query($sql);

$rows = [];
if ($result->num_rows > 0) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Tidak ada data dalam tabel penjualan.";
}
$conn->close();


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
    <h2>Invoice Penjualan</h2>
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
?>

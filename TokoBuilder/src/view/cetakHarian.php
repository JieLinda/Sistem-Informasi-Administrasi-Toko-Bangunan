
<?php
require '../logic/tools.php';
require '../logic/vendor/autoload.php';

use Dompdf\Dompdf;

function harian($data){
    global $conn;
    $transaction_date = $data['transaction_date'];

    $harian = mysqli_query($conn,"SELECT j.jurnal_id, p.product_name, p.product_price,j.amount_sold, 
                    j.total, j.transaction_date FROM jurnal j join produk p on 
                    j.product_id = p.product_id WHERE transaction_date = '$transaction_date'");
    return $harian->fetch_all(MYSQLI_ASSOC);
}

if(isset($_POST['ha'])){
    $mingguan = $_POST['harian'];
    $rows = harian(['transaction_date' => $mingguan]);

    if (empty($rows)) {
        echo "Tidak ada data untuk tanggal $mingguan.";
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
        <h2>Invoice Penjualan - ' . htmlspecialchars($mingguan) . '</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Harga Produk</th>
                    <th>Jumlah</th>
                    <th>Total</th>
                    <th>Tanggal Transaksi</th>
                </tr>
            </thead>
            <tbody>
    ';

    foreach ($rows as $row) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($row['product_name']) . '</td>
                    <td>' . number_format($row['product_price']) . '</td>
                    <td>' . number_format($row['amount_sold']) . '</td>
                    <td>' . number_format($row['total']) . '</td>
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
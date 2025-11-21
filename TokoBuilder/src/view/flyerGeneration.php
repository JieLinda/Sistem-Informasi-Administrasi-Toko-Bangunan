<?php 

include '../logic/tools.php';
require_once '../logic/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Fetch data from database
$selectOptions = $_POST['options'];
$option = new Options;
$option->setChroot("../");

// Start HTML output
$html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <style>
        .footer {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            background-color: #4A4A4A;
            color: white;
            text-align: center;
            padding: 10px 20px;
            font-size: 1.2rem;
            font-weight: bold;
            border-radius: 10px 10px 0 0;
            box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }
    </style>
</head>
<body style="margin: 0; font-family: Arial, sans-serif; background-color: #f4f4f9; color: #333;">
    <div style="display: table; width: 100%; padding: 20px;">';
if ($_POST['discount'] == "diskon") {
    foreach ($selectOptions as $selectOption) {
        $sql = "SELECT * FROM produk WHERE product_id = $selectOption";
        $result = $conn->query($sql);    
        $row = $result->fetch_assoc();
        $imagePath = '../images/' . htmlspecialchars($row['image_path'], ENT_QUOTES, 'UTF-8');
        $price = $row['product_price'];
        $disc = $price - $price*0.05;
        
        $html .= '<div style="display: inline-block; width: 18%; margin: 1%; background-color: #ffffff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); text-align: center; padding: 10px; vertical-align: top;">
            <div style="margin-bottom: 10px;">
                <img src="' . $imagePath . '" alt="Product Image" style="width: 100%; height: auto; max-height: 150px; object-fit: cover; border-bottom: 1px solid #eaeaea;">
            </div>
            <h2 style="font-size: 1.2rem; font-weight: bold; margin: 0; color: #333;">' . htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8') . '</h2>
            <p style="font-size: 1rem; text-decoration: line-through; text-decoration-color: red;
            text-decoration-thickness: 2px; color: #007BFF; margin-top: 5px;">Rp ' . number_format($price, 0, ',', '.') . '</p>
            <p style="font-size: 1rem; 
             color: #007BFF; margin-top: 5px;">Rp ' . number_format($disc, 0, ',', '.') . '</p>
        </div>';
    }
}
else {
    foreach ($selectOptions as $selectOption) {
        $sql = "SELECT * FROM produk WHERE product_id = $selectOption";
        $result = $conn->query($sql);    
        $row = $result->fetch_assoc();
        $imagePath = '../images/' . htmlspecialchars($row['image_path'], ENT_QUOTES, 'UTF-8');
        $price = $row['product_price'];
        
        $html .= '<div style="display: inline-block; width: 18%; margin: 1%; background-color: #ffffff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); text-align: center; padding: 10px; vertical-align: top;">
            <div style="margin-bottom: 10px;">
                <img src="' . $imagePath . '" alt="Product Image" style="width: 100%; height: auto; max-height: 150px; object-fit: cover; border-bottom: 1px solid #eaeaea;">
            </div>
            <h2 style="font-size: 1.2rem; font-weight: bold; margin: 0; color: #333;">' . htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8') . '</h2>
                        
            <p style="font-size: 1rem; color: #007BFF; margin-top: 5px;">Rp ' . number_format($price, 0, ',', '.') . '</p>
        </div>';
    }
}



$html .= '</div><div class="footer">Sistem Informasi</div></body></html>';

$dompdf = new Dompdf($option);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('katalog.pdf', ["Attachment" => 0]);
?>




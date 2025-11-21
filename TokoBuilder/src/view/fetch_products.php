<?php
include '../logic/tools.php';
session_start();
if(!isset($_SESSION['username'])){
    header("Location: ../headerManager/login.php");
}

if (isset($_POST['supplier_id'])) {
    $supplier_id = (int)$_POST['supplier_id'];
    
    
    $result = $conn->query("SELECT * FROM produk_yang_disupply d join supplier s on d.supplier_id = s.supplier_id join produk p on d.product_id = p.product_id 
    where d.supplier_id = $supplier_id");
    
    


    if ($result->num_rows > 0) {
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        echo json_encode($products);  // Return products as JSON
    } else {
        echo json_encode([]);  // Return empty array if no products found
    }
}
?>

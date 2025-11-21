<?php

require 'tools.php';


if ( !isset($_SESSION['username']) ){
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];

defined("VALID_ACCESS") or die("Cannot access this script directly");

function get_all_suppliers(mysqli $conn): array {
    $result = $conn->query("SELECT supplier_id, supplier_name FROM Supplier");
    $data = $result->fetch_all(MYSQLI_ASSOC);
    return $data;
}

function get_order_restock_by_nota_id(mysqli $conn, int $nota_id): array {
    $query = "
            SELECT 
                p.product_name,
            or_.order_restock_price,
            or_.order_restock_qty,
            or_. order_restock_delivered_qty,
            (or_.order_restock_price * or_.order_restock_qty) AS 'total_payment',
            or_.order_restock_delivery_status,
            or_.order_restock_id
        FROM Order_Restock or_
        JOIN 
            Produk p ON or_.produk_id = p.product_id
        JOIN 
            Nota_Pembelian np ON np.nota_id = or_.nota_id
        WHERE np.nota_id = ?
    ";
    
    $result = $conn->prepare($query);
    $result->execute([$nota_id]);
    $data = $result->get_result()->fetch_all(MYSQLI_ASSOC);
    return $data;
}

function update_delivered_by_order_restock_id(mysqli $conn, array $data): void {
    $query = "UPDATE Order_Restock SET order_restock_delivered_qty = ? WHERE order_restock_id = ?";
    foreach ($data as $item) {
        $conn->prepare($query)->execute([$item['value'], $item['id']]);
        if ($item['is_fully_delivered']) {
            $conn->prepare("UPDATE Order_Restock SET order_restock_delivery_status = 'Delivered' WHERE order_restock_id = ?")->execute([$item['id']]);
        }
    }
}

function update_hutang_by_nota_id(mysqli $conn, int $nota_id, float $paid_amount): void {
    $query = "UPDATE Hutang SET debt_amount = debt_amount - ? WHERE nota_id = ?";
    $conn->prepare($query)->execute([$paid_amount, $nota_id]);
}

function get_all_products(mysqli $conn): array {
    $result = $conn->query("SELECT * FROM Produk");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function create_sales_record(mysqli $conn, string $tanggal, array $products): void {
    $conn->begin_transaction();
    
    try {
        $stmt = $conn->prepare("INSERT INTO jurnal (transaction_date) VALUES (?)");
        $stmt->execute([$tanggal]);
        
        $catatan_penjualan_id = $conn->insert_id;
        
        $detail_stmt = $conn->prepare(
            "INSERT INTO Penjualan_Detail (produk_id, jurnal_id, penjualan_detail_qty) 
             VALUES (?, ?, ?)"
        );
        
        foreach ($products as $product) {
            $detail_stmt->execute([
                $product['produk_id'], 
                $catatan_penjualan_id, 
                $product['qty']
            ]);
        }
        
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
}

function get_sales_summary(mysqli $conn): array {
    $query = "
    SELECT 
        cp.jurnal_id, 
        cp.transaction_date, 
        SUM(p.harga_jual * pd.penjualan_detail_qty) AS total_transaksi, 
        SUM(pd.penjualan_detail_qty) AS total_qty
    FROM jurnal cp
    JOIN Penjualan_Detail pd ON cp.jurnal_id = pd.jurnal_id
    JOIN Produk p ON pd.produk_id = p.produk_id
    GROUP BY cp.jurnal_id, cp.transaction_date
    ORDER BY cp.jurnal_id

    ";
    
    $result = $conn->query($query);
    if (!$result) {
        throw new Exception('Query Error: ' . $conn->error);
    }
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

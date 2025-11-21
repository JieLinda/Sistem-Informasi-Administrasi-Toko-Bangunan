<?php
require 'tools.php';
require 'email.php';

function checkInventory() {
    $products = query("SELECT * FROM warning_stock left join produk on warning_stock.product_id = produk.product_id");
    foreach ($products as $product) {
        if ($product['stock'] < $product['min_stock'] and date($product['last_email'])!=date('Y-m-d')) {
            sendStockWarning('frosttesla@gmail.com', $product['product_name'],$product['stock'],$product['min_stock']);
            updateSentEmail(date('Y-m-d'),'warning_stock',$product['warning_hutang_id'], 'product_id');
        }
        else if ($product['stock'] >= $product['min_stock']) {
            query("DELETE FROM warning_stock WHERE product_id = $product[product_id]");
        }
    }
} 

function checkPiutang() {
    $piutangs = query("SELECT * FROM warning_piutang left join piutang on warning_piutang.receivable_id = piutang.receivable_id join customer on piutang.customer_id = customer.customer_id");
    foreach ($piutangs as $piutang) {
        if (date($piutang['deadline'])<=date('Y-m-d') and date($piutang['last_email'])!=date('Y-m-d')) {
            sendPiutangWarning('frosttesla@gmail.com', $piutang['receivable_amount'],$piutang['customer_name'],$piutang['deadline']);
            updateSentEmail(date('Y-m-d'),'warning_piutang',$piutang['receivable_id'], 'receivable_id');
        }
    }
}

function checkHutang() {
    $hutangs = query("SELECT * FROM warning_hutang left join hutang on warning_hutang.debt_id = hutang.debt_id join supplier on hutang.supplier_id = supplier.supplier_id");
    foreach ($hutangs as $hutang) {
        if (date($hutang['deadline'])<=date('Y-m-d') and date($hutang['last_email'])!=date('Y-m-d')) {
            sendHutangWarning('frosttesla@gmail.com', $hutang['debt_amount'],$hutang['supplier_name'],$hutang['deadline']);
            updateSentEmail(date('Y-m-d'),'warning_hutang',$hutang['debt_id'], 'debt_id');
        }
    }
}
 
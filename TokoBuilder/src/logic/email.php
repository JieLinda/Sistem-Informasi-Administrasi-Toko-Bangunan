<?php
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
// Looking to send emails in production? Check out our Email API/SMTP product!
$phpmailer = new PHPMailer();
$phpmailer->isSMTP();
$phpmailer->isHTML(true);
$phpmailer->Host = 'smtp.gmail.com';
$phpmailer->SMTPAuth = true;
$phpmailer->Port = 587;
$phpmailer->Username = 'ukptugas@gmail.com';
$phpmailer->Password = 'bvvi yqyn regf gazd';
$phpmailer->setFrom('ukptugas@gmail.com', 'Sistem Informasi');
$phpmailer->addReplyTo('ukptugas@gmail.com', 'Information');
$phpmailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;


function sendStockWarning($to, $item, $currStock, $minStock) {
    global $phpmailer;
    $phpmailer->addAddress($to);
    $phpmailer->Subject = 'Stock warning for ' . $item;
    $phpmailer->Body = 'Please restock ' . $item. ' soon!'.'<br>Current Stock: '.$currStock.'<br>Minimum Stock: '.$minStock;

    if(!$phpmailer->send()) {
        echo 'Mailer Error: ' . $phpmailer->ErrorInfo;
    }
}

function sendPiutangWarning($to, $piutang, $cust, $dueDate) {
    global $phpmailer;
    $phpmailer->addAddress($to);
    $phpmailer->Subject = 'Piutang warning for ' . $cust;
    $phpmailer->Body = 'Please collect piutang for ' . $cust. ' soon!'.'<br>Due Date: '.$dueDate.'<br>Total Piutang: '.$piutang
    ."<br>Overdue: ".date_diff(date_create(date('Y-m-d')), date_create($dueDate)) ->format('%R%a days');
    
    if(!$phpmailer->send()) {
        echo 'Mailer Error: ' . $phpmailer->ErrorInfo;
    }
}

function sendHutangWarning($to, $hutang, $supplier, $dueDate) {
    global $phpmailer;
    $phpmailer->addAddress($to);
    $phpmailer->Subject = 'Hutang warning for ' . $supplier;
    $phpmailer->Body = 'Please pay hutang for ' . $supplier. ' soon!'.'<br>Due Date: '.$dueDate.'<br>Total Hutang: '.$hutang
    ."<br>Overdue: ".date_diff(date_create(date('Y-m-d')), date_create($dueDate)) ->format('%R%a days');
    if(!$phpmailer->send()) {
        echo 'Mailer Error: ' . $phpmailer->ErrorInfo;
    }
}

function updateSentEmail($newDate, $tableName, $id, $type) {
    $query = "UPDATE $tableName SET last_email = '$newDate' WHERE $type = $id";
    query_no_return($query);
}
<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['invoice_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$invoice_id = (int)$_POST['invoice_id'];
$patient_id = $_SESSION['user_id'];

// LOGIC FROM REPO: 
// 1. Update status to 'paid'
// 2. Set payment_method to 'Online' (Since this is the online portal)
$sql = "UPDATE invoices 
        SET status = 'paid', payment_method = 'Online' 
        WHERE id = '$invoice_id' AND patient_id = '$patient_id'";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

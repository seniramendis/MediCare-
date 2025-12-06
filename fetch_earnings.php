<?php

session_start();
include 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['revenue' => 0, 'total' => 0, 'completed' => 0, 'pending' => 0]);
    exit();
}

$doctor_id = $_SESSION['user_id'];


$rev = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as t FROM payments WHERE doctor_id='$doctor_id'"))['t'] ?? 0;


$pat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT patient_id) as c FROM appointments WHERE doctor_id='$doctor_id'"))['c'];


$comp = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM appointments WHERE doctor_id='$doctor_id' AND status='Completed'"))['c'];


$pend = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM appointments WHERE doctor_id='$doctor_id' AND status IN ('Scheduled', 'Pending', 'Confirmed')"))['c'];

echo json_encode([
    'revenue' => number_format($rev, 2),
    'total' => $pat,
    'completed' => $comp,
    'pending' => $pend
]);

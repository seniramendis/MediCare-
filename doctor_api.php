<?php
// doctor_api.php
// 1. SILENCE ERRORS & BUFFER OUTPUT
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

session_start();
include 'db_connect.php';
date_default_timezone_set('Asia/Colombo');

// 2. WIPE THE BUFFER (Removes any accidental whitespace/errors)
ob_clean();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    echo json_encode([]);
    exit();
}

$doctor_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

// --- FETCH APPOINTMENTS ---
if ($action === 'fetch') {
    // CORRECT QUERY: Join 'users' table to get the name from 'patient_id'
    $sql = "SELECT a.*, u.full_name 
            FROM appointments a 
            LEFT JOIN users u ON a.patient_id = u.id 
            WHERE a.doctor_id = '$doctor_id' 
            ORDER BY a.appointment_time ASC";

    $result = mysqli_query($conn, $sql);
    $data = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // If full_name is found via join, use it. Otherwise fallback.
            $name = !empty($row['full_name']) ? $row['full_name'] : "Patient #" . $row['patient_id'];

            $row['patient_name'] = $name;
            $row['formatted_time'] = date('M d, h:i A', strtotime($row['appointment_time']));
            // Normalize Status
            $row['status'] = ucfirst(strtolower($row['status']));

            $data[] = $row;
        }
    }

    // 3. SEND CLEAN JSON
    echo json_encode($data);
    exit();
}

// --- UPDATE STATUS ---
if ($action === 'update') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $sql = "UPDATE appointments SET status='$status' WHERE id='$id' AND doctor_id='$doctor_id'";
    mysqli_query($conn, $sql);
    echo json_encode(['status' => 'success']);
    exit();
}

// --- DELETE APPOINTMENT ---
if ($action === 'delete') {
    $id = $_POST['id'];
    $sql = "DELETE FROM appointments WHERE id='$id' AND doctor_id='$doctor_id'";
    mysqli_query($conn, $sql);
    echo json_encode(['status' => 'success']);
    exit();
}

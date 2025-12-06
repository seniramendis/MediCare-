<?php

session_start();
include 'db_connect.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {

    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}


if (!isset($_POST['appointment_id']) || !is_numeric($_POST['appointment_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid appointment ID.']);
    exit();
}

$appointment_id = (int)$_POST['appointment_id'];
$doctor_id = $_SESSION['user_id'];


$sql = "UPDATE appointments 
        SET status = 'completed' 
        WHERE id = ? AND doctor_id = ?";


if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $appointment_id, $doctor_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {

            echo json_encode(['success' => true, 'message' => 'Appointment marked as completed.']);
        } else {

            echo json_encode(['success' => false, 'message' => 'Appointment not found or update failed.']);
        }
    } else {

        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
    $stmt->close();
} else {

    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database preparation error.']);
}

$conn->close();
header('Content-Type: application/json');

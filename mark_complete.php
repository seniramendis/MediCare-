<?php
// --- SECURITY & SESSION CHECK ---
session_start();
include 'db_connect.php'; // Ensure this file exists and connects to your DB

// Check if user is logged in AND is a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    // Send a 403 Forbidden response for API calls
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

// --- INPUT VALIDATION ---
if (!isset($_POST['appointment_id']) || !is_numeric($_POST['appointment_id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Invalid appointment ID.']);
    exit();
}

$appointment_id = (int)$_POST['appointment_id'];
$doctor_id = $_SESSION['user_id'];

// --- DATABASE UPDATE ---
// IMPORTANT: Only update if the appointment belongs to the logged-in doctor
$sql = "UPDATE appointments 
        SET status = 'completed' 
        WHERE id = ? AND doctor_id = ?";

// Use prepared statements for security
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $appointment_id, $doctor_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Success response
            echo json_encode(['success' => true, 'message' => 'Appointment marked as completed.']);
        } else {
            // Appointment not found or already completed
            echo json_encode(['success' => false, 'message' => 'Appointment not found or update failed.']);
        }
    } else {
        // Database execution error
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    // Prepared statement error
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database preparation error.']);
}

$conn->close();
header('Content-Type: application/json');

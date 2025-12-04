<?php
// 1. Start Session & Connect to DB
session_start();
include 'db_connect.php';

// 2. SECURITY CHECK (MUST BE BEFORE HEADER HTML)
// This kicks the Admin out immediately so they never see the Patient Dashboard or the "System Admin" name here.
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit();
}

// 3. NOW it is safe to load the visual header
include 'header.php';

// 4. Page Logic
$page_title = "Dashboard";
date_default_timezone_set('Asia/Colombo');

$patient_id = $_SESSION['user_id'];
$patient_name = $_SESSION['username'];

// ... (Rest of your fetching logic) ...
// Dynamic Greeting
$hour = date('H');
if ($hour < 12) $greeting = "Good Morning";
elseif ($hour < 18) $greeting = "Good Afternoon";
else $greeting = "Good Evening";

// Fetch Next Appointment
$sql_next = "SELECT * FROM appointments WHERE patient_id = '$patient_id' AND appointment_time >= NOW() ORDER BY appointment_time ASC LIMIT 1";
$result_next = mysqli_query($conn, $sql_next);
$next_appt = mysqli_fetch_assoc($result_next);

// Fetch History
$sql_all = "SELECT * FROM appointments WHERE patient_id = '$patient_id' ORDER BY id DESC";
$result_all = mysqli_query($conn, $sql_all);
?>

<style>
    /* ... (Keep your existing CSS styles here) ... */
    :root {
        --dash-bg: #f8f9fa;
        --card-bg: #ffffff;
        --success-color: #10b981;
    }

    body {
        background-color: var(--dash-bg);
    }

    .dash-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }

    /* ... (Rest of CSS) ... */
</style>

<div class="dash-container">
    <h1><?php echo $greeting . ", " . htmlspecialchars($patient_name); ?>!</h1>
</div>

<?php include 'footer.php'; ?>
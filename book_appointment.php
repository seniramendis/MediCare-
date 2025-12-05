<?php
// --- 1. SESSION & SECURITY CHECK ---
session_start();

// If the user is NOT logged in, kick them to the login page immediately.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit(); // Stop this script from running
}

// --- 2. PAGE SETUP ---
$page_title = "Book Appointment";
include 'header.php';
include 'db_connect.php';

$message = "";
$msg_type = "";

// --- 3. FORM HANDLING ---
if (isset($_POST['book_appointment'])) {
    // Collect Inputs
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    $doctor_id = mysqli_real_escape_string($conn, $_POST['doctor']);

    // Combine Date & Time for SQL
    $date = $_POST['date'];
    $time = $_POST['time'];
    $combined_datetime = $date . ' ' . $time . ':00'; // Format: YYYY-MM-DD HH:MM:SS

    // Get Patient ID (We are 100% sure this exists now because of the check at the top)
    $patient_id = $_SESSION['user_id'];

    // Insert into Database
    // Note: We use the logged-in ID for 'patient_id'
    $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_time, reason, status) 
            VALUES ('$patient_id', '$doctor_id', '$combined_datetime', '$reason', 'pending')";

    if (mysqli_query($conn, $sql)) {
        $message = "Appointment Booked Successfully! We will contact you shortly.";
        $msg_type = "success";
    } else {
        $message = "Error: " . mysqli_error($conn);
        $msg_type = "error";
    }
}

// Fetch Doctors for the dropdown
$doc_sql = "SELECT * FROM doctors";
$doc_result = mysqli_query($conn, $doc_sql);
?>

<style>
    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #eef2ff 100%);
        padding: 60px 0;
        text-align: center;
        margin-bottom: 50px;
    }

    .page-header h1 {
        font-size: 42px;
        color: #0c5adb;
        font-weight: 800;
    }

    .page-header p {
        color: #6b7280;
        font-size: 18px;
    }

    /* Main Container */
    .booking-wrapper {
        max-width: 1100px;
        margin: 0 auto 80px auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 0;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        border-radius: 20px;
        overflow: hidden;
        background: white;
    }

    /* Form Section */
    .booking-form {
        padding: 50px;
    }

    .form-title {
        font-size: 24px;
        color: #1f2937;
        margin-bottom: 30px;
        font-weight: 700;
        border-left: 5px solid #0c5adb;
        padding-left: 15px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        color: #1f2937;
        margin-bottom: 8px;
        font-weight: 500;
        font-size: 14px;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 15px;
        outline: none;
        transition: 0.3s;
        background: #f9fafb;
    }

    .form-control:focus {
        border-color: #0c5adb;
        background: white;
        box-shadow: 0 0 0 4px rgba(12, 90, 219, 0.05);
    }

    .row-inputs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    textarea.form-control {
        resize: none;
        height: 100px;
    }

    .btn-submit {
        width: 100%;
        padding: 15px;
        background: #0c5adb;
        color: white;
        border: none;
        border-radius: 50px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s;
        margin-top: 10px;
    }

    .btn-submit:hover {
        background: #0946a8;
        transform: translateY(-2px);
    }

    /* Info Section (Right Side) */
    .booking-info {
        background: #0c5adb;
        color: white;
        padding: 50px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    /* Decorative Circles */
    .booking-info::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .booking-info::after {
        content: '';
        position: absolute;
        bottom: -50px;
        left: -50px;
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .info-item {
        margin-bottom: 30px;
        position: relative;
        z-index: 1;
    }

    .info-item i {
        font-size: 24px;
        margin-bottom: 15px;
        display: block;
        opacity: 0.8;
    }

    .info-item h3 {
        font-size: 20px;
        margin-bottom: 10px;
    }

    .info-item p {
        opacity: 0.8;
        font-size: 15px;
        line-height: 1.6;
    }

    /* Alerts */
    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 500;
    }

    .alert-success {
        background: #dcfce7;
        color: #16a34a;
        border: 1px solid #bbf7d0;
    }

    .alert-error {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .booking-wrapper {
            grid-template-columns: 1fr;
        }

        .row-inputs {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-header">
    <h1>Book an Appointment</h1>
    <p>Schedule a consultation with our expert doctors.</p>
</div>

<div class="booking-wrapper">
    <div class="booking-form">
        <h2 class="form-title">Appointment Details</h2>
        <?php if ($message != ""): ?>
            <div class="alert alert-<?php echo $msg_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="row-inputs">
                <div class="form-group">
                    <label>Patient Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Full Name"
                        value="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control" placeholder="07x xxxxxxx" required>
                </div>
            </div>

            <div class="row-inputs">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
                </div>
                <div class="form-group">
                    <label>Select Doctor</label>
                    <select name="doctor" class="form-control" required>
                        <option value="" disabled selected>Choose a Specialist</option>
                        <?php
                        if (mysqli_num_rows($doc_result) > 0) {
                            while ($row = mysqli_fetch_assoc($doc_result)) {
                                echo "<option value='" . $row['id'] . "'>" . $row['name'] . " (" . $row['specialty'] . ")</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="row-inputs">
                <div class="form-group">
                    <label>Preferred Date</label>
                    <input type="date" name="date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label>Preferred Time</label>
                    <input type="time" name="time" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label>Reason for Visit</label>
                <textarea name="reason" class="form-control" placeholder="Briefly describe your symptoms or reason for appointment..."></textarea>
            </div>

            <button type="submit" name="book_appointment" class="btn-submit">Confirm Appointment</button>
        </form>
    </div>

    <div class="booking-info">
        <div class="info-item">
            <i class="fas fa-phone-alt"></i>
            <h3>Emergency Cases</h3>
            <p>For immediate medical attention, please do not use this form. Call our emergency hotline immediately.</p>
            <p style="font-size: 20px; font-weight: 700; margin-top: 10px;">1990 / +94 112 345 678</p>
        </div>
        <div class="info-item">
            <i class="fas fa-clock"></i>
            <h3>Working Hours</h3>
            <p>Monday - Friday: 8:00 AM - 9:00 PM</p>
            <p>Saturday: 9:00 AM - 5:00 PM</p>
            <p>Sunday: Closed</p>
        </div>
        <div class="info-item">
            <i class="fas fa-info-circle"></i>
            <h3>Need Help?</h3>
            <p>If you have trouble booking online, our front desk support team is available to assist you during working hours.</p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
<?php
$page_title = "Book Appointment";
include 'header.php';
include 'db_connect.php';

// --- 1. HANDLE FORM SUBMISSION ---
$message = "";
$msg_type = ""; // 'success' or 'error'

if (isset($_POST['book_appointment'])) {
    // Collect Data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $doctor = mysqli_real_escape_string($conn, $_POST['doctor']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);

    // Check if user is logged in (to save user_id)
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "NULL";

    // Insert into Database
    $sql = "INSERT INTO appointments (user_id, patient_name, email, phone, doctor_name, appointment_date, appointment_time, reason) 
            VALUES ($user_id, '$name', '$email', '$phone', '$doctor', '$date', '$time', '$reason')";

    if (mysqli_query($conn, $sql)) {
        $message = "Appointment Booked Successfully! We will contact you shortly.";
        $msg_type = "success";
    } else {
        $message = "Error: " . mysqli_error($conn);
        $msg_type = "error";
    }
}

// --- 2. FETCH DOCTORS FOR DROPDOWN ---
$doc_sql = "SELECT * FROM doctors";
$doc_result = mysqli_query($conn, $doc_sql);
?>

<style>
    /* ===== PAGE HEADER ===== */
    .page-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #eef2ff 100%);
        padding: 60px 0;
        text-align: center;
        margin-bottom: 50px;
    }

    .page-header h1 {
        font-size: 42px;
        color: var(--primary-color);
        font-weight: 800;
    }

    .page-header p {
        color: var(--text-light);
        font-size: 18px;
    }

    /* ===== BOOKING CONTAINER ===== */
    .booking-wrapper {
        max-width: 1100px;
        margin: 0 auto 80px auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        /* Form takes more space */
        gap: 0;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        border-radius: 20px;
        overflow: hidden;
        background: white;
    }

    /* LEFT SIDE: FORM */
    .booking-form {
        padding: 50px;
    }

    .form-title {
        font-size: 24px;
        color: var(--text-dark);
        margin-bottom: 30px;
        font-weight: 700;
        border-left: 5px solid var(--primary-color);
        padding-left: 15px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        color: var(--text-dark);
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
        border-color: var(--primary-color);
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
        background: var(--primary-color);
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
        background: var(--primary-dark);
        transform: translateY(-2px);
    }

    /* RIGHT SIDE: INFO PANEL */
    .booking-info {
        background: var(--primary-color);
        color: white;
        padding: 50px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    /* Decorative circles */
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

    /* ALERTS */
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

    <!-- LEFT: FORM -->
    <div class="booking-form">
        <h2 class="form-title">Appointment Details</h2>

        <?php if ($message != ""): ?>
            <div class="alert alert-<?php echo $msg_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <!-- Row 1: Name & Phone -->
            <div class="row-inputs">
                <div class="form-group">
                    <label>Patient Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Full Name"
                        value="<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control" placeholder="07x xxxxxxx" required>
                </div>
            </div>

            <!-- Row 2: Email & Doctor -->
            <div class="row-inputs">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
                </div>
                <div class="form-group">
                    <label>Select Doctor</label>
                    <select name="doctor" class="form-control" required>
                        <option value="" disabled selected>Choose a Specialist</option>
                        <!-- PHP Loop to fill Dropdown -->
                        <?php
                        if (mysqli_num_rows($doc_result) > 0) {
                            while ($row = mysqli_fetch_assoc($doc_result)) {
                                echo "<option value='" . $row['name'] . "'>" . $row['name'] . " (" . $row['specialty'] . ")</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Row 3: Date & Time -->
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

            <!-- Reason -->
            <div class="form-group">
                <label>Reason for Visit</label>
                <textarea name="reason" class="form-control" placeholder="Briefly describe your symptoms or reason for appointment..."></textarea>
            </div>

            <button type="submit" name="book_appointment" class="btn-submit">Confirm Appointment</button>
        </form>
    </div>

    <!-- RIGHT: INFO -->
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
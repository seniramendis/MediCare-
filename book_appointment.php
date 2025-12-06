<?php
ob_start();
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$msg = "";
$patient_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_id = $_POST['doctor_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $reason = mysqli_real_escape_string($conn, $_POST['message']);
    $full_time = date('Y-m-d H:i:s', strtotime("$date $time"));

    // FIXED QUERY: Removed 'patient_name'. Only inserting 'patient_id'.
    $sql = "INSERT INTO appointments (doctor_id, patient_id, appointment_time, reason, status) 
            VALUES ('$doctor_id', '$patient_id', '$full_time', '$reason', 'Scheduled')";

    if (mysqli_query($conn, $sql)) {
        $msg = "<div style='background:#dbeafe; color:#1e40af; padding:15px; border-radius:8px; margin-bottom:20px; border: 1px solid #bfdbfe; display:flex; align-items:center; gap:10px;'>
                    <i class='fas fa-check-circle'></i> 
                    <div><strong>Success!</strong> Request sent. Waiting for Doctor to accept.</div>
                </div>";
    } else {
        $msg = "<div style='background:#fee2e2; color:#b91c1c; padding:15px; border-radius:8px; margin-bottom:20px;'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Book Appointment | Medicare+</title>
    <link rel="icon" href="images/Favicon.png" type="image/png">
    <script src="https://kit.fontawesome.com/9e166a3863.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* --- BLUE THEME STYLING --- */
        :root {
            --primary-color: #0c5adb;
            --primary-dark: #0946a8;
            --bg-color: #f8f9fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-color);
            margin: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(12, 90, 219, 0.1);
        }

        h2 {
            color: var(--primary-color);
            border-bottom: 3px solid var(--primary-color);
            display: inline-block;
            padding-bottom: 5px;
            margin-top: 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 25px;
        }

        .full-width {
            grid-column: span 2;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
            font-size: 14px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-sizing: border-box;
            font-family: inherit;
            background: #f8fafc;
            transition: 0.3s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--primary-color);
            background: white;
            outline: none;
            box-shadow: 0 0 0 3px rgba(12, 90, 219, 0.1);
        }

        button {
            background: var(--primary-color);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            margin-top: 30px;
            width: 100%;
            transition: 0.3s;
            box-shadow: 0 4px 15px rgba(12, 90, 219, 0.3);
        }

        button:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #888;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            color: var(--primary-color);
        }
    </style>
</head>

<body>

    <?php include 'header.php'; ?>

    <div class="container">
        <?php echo $msg; ?>

        <h2>Book an Appointment</h2>
        <p style="color:#666;">Fill in the form below to schedule a visit with our specialists.</p>

        <form method="POST" action="">
            <div class="form-grid">
                <div class="full-width">
                    <label>Select Doctor</label>
                    <select name="doctor_id" required>
                        <option value="">-- Choose a Specialist --</option>
                        <?php
                        // Ensure we only fetch users who are actually doctors
                        $doc_res = mysqli_query($conn, "SELECT id, full_name, specialty FROM users WHERE role='doctor'");
                        while ($row = mysqli_fetch_assoc($doc_res)) {
                            // Display Doctor Name and Specialty
                            $specialty = !empty($row['specialty']) ? " (" . $row['specialty'] . ")" : "";
                            echo "<option value='" . $row['id'] . "'>Dr. " . $row['full_name'] . $specialty . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label>Preferred Date</label>
                    <input type="date" name="date" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div>
                    <label>Preferred Time</label>
                    <input type="time" name="time" required>
                </div>
                <div class="full-width">
                    <label>Reason for Visit</label>
                    <textarea name="message" rows="4" placeholder="Briefly describe your symptoms..." required></textarea>
                </div>
            </div>
            <button type="submit">Confirm Booking</button>
            <a href="dashboard_patient.php" class="back-link">Cancel and Return</a>
        </form>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>
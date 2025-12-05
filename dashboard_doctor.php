<?php
session_start();
include 'db_connect.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit();
}

// --- 1. INIT DATA ---
$doctor_id = $_SESSION['user_id'];
$doctor_name = $_SESSION['username'];
$display_name = trim(str_ireplace("Dr.", "", $doctor_name));

// Time Greeting
$h = date('H');
$greeting = ($h < 12) ? "Good Morning" : (($h < 18) ? "Good Afternoon" : "Good Evening");

// --- 2. LOGIC: SEND INVOICE (With 2-Bill Limit from Repo) ---
if (isset($_POST['send_bill'])) {
    $appt_val = $_POST['bill_appt_id'];
    list($appt_id, $pat_id) = explode('_', $appt_val);
    $service = mysqli_real_escape_string($conn, $_POST['service_desc']);
    $amount = $_POST['amount'];

    // CHECK LIMIT: Max 2 Invoices per Appointment
    $check_limit = mysqli_query($conn, "SELECT COUNT(*) as count FROM invoices WHERE appointment_id = '$appt_id'");
    $limit_row = mysqli_fetch_assoc($check_limit);

    if ($limit_row['count'] >= 2) {
        echo "<script>alert('⚠️ Limit Reached: You can only send 2 invoices for this appointment.');</script>";
    } else {
        // Insert Invoice
        $sql = "INSERT INTO invoices (patient_id, doctor_id, appointment_id, amount, service_name, status) 
                VALUES ('$pat_id', '$doctor_id', '$appt_id', '$amount', '$service', 'unpaid')";
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('✅ Invoice Sent Successfully!');</script>";
        }
    }
}

// --- 3. LOGIC: SEND PRESCRIPTION ---
if (isset($_POST['send_rx'])) {
    $p_id = $_POST['rx_patient_id'];
    $diag = mysqli_real_escape_string($conn, $_POST['diagnosis']);
    $meds = mysqli_real_escape_string($conn, $_POST['medication']);
    $sql = "INSERT INTO prescriptions (doctor_id, patient_id, diagnosis, medicine_list, dosage_instructions) 
            VALUES ('$doctor_id', '$p_id', '$diag', 'General Rx', '$meds')";
    if (mysqli_query($conn, $sql)) echo "<script>alert('✅ Prescription Sent!');</script>";
}

// --- 4. STATS & EARNINGS (Logic from fetch_earnings.php) ---
// Calculate Total Earnings (Sum of payments table)
$earning_sql = "SELECT SUM(p.amount) as total_earned 
                FROM payments p 
                JOIN invoices i ON p.invoice_id = i.id 
                WHERE i.doctor_id = '$doctor_id'";
$earning_res = mysqli_query($conn, $earning_sql);
$earning_row = mysqli_fetch_assoc($earning_res);
$total_revenue = $earning_row['total_earned'] ? $earning_row['total_earned'] : 0;

// Other Counts
$count_pat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT patient_id) as c FROM appointments WHERE doctor_id='$doctor_id'"))['c'];
$count_done = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM appointments WHERE doctor_id='$doctor_id' AND status='Completed'"))['c'];
$count_pend = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM appointments WHERE doctor_id='$doctor_id' AND status='Scheduled'"))['c'];

// --- 5. FETCH DATA FOR TABLES ---
$recent_payments = mysqli_query($conn, "SELECT p.*, u.full_name FROM payments p JOIN users u ON p.patient_id = u.id WHERE p.doctor_id='$doctor_id' ORDER BY p.paid_at DESC LIMIT 5");
$incoming_appts = mysqli_query($conn, "SELECT a.*, u.full_name FROM appointments a JOIN users u ON a.patient_id = u.id WHERE a.doctor_id='$doctor_id' AND a.status IN ('Scheduled', 'Confirmed') ORDER BY a.appointment_time ASC");
$patients = mysqli_query($conn, "SELECT DISTINCT u.id, u.full_name FROM users u JOIN appointments a ON u.id = a.patient_id WHERE a.doctor_id='$doctor_id'");
$appts = mysqli_query($conn, "SELECT a.id, a.patient_id, u.full_name, a.appointment_time FROM appointments a JOIN users u ON a.patient_id = u.id WHERE a.doctor_id='$doctor_id' AND a.status='Confirmed'");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Doctor Dashboard</title>
    <?php include 'header.php'; ?>
    <style>
        /* ELEGANT THEME STYLES */
        body {
            background: linear-gradient(rgba(248, 249, 250, 0.9), rgba(248, 249, 250, 0.9)), url('images/background3.png');
            background-size: cover;
            background-attachment: fixed;
        }

        .dash-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            animation: fadeIn 0.8s ease;
        }

        .welcome-banner {
            background: linear-gradient(135deg, #0c5adb, #0946a8);
            color: white;
            padding: 40px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(12, 90, 219, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .icon-blue {
            background: #e0f2fe;
            color: #0284c7;
        }

        .icon-green {
            background: #dcfce7;
            color: #16a34a;
        }

        .icon-orange {
            background: #ffedd5;
            color: #ea580c;
        }

        .icon-gold {
            background: #fef9c3;
            color: #ca8a04;
        }

        /* Gold for Earnings */

        .content-box {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .forms-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #f9fafb;
        }

        .btn-action {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: none;
            color: white;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-blue {
            background: #0c5adb;
        }

        .btn-green {
            background: #16a34a;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        th {
            text-align: left;
            padding: 15px;
            color: #6b7280;
            font-size: 14px;
        }

        td {
            background: #f9fafb;
            padding: 15px;
            border-top: 1px solid #f3f4f6;
            border-bottom: 1px solid #f3f4f6;
        }

        tr td:first-child {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
            border-left: 1px solid #f3f4f6;
        }

        tr td:last-child {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
            border-right: 1px solid #f3f4f6;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <div class="dash-container">
        <div class="welcome-banner">
            <div>
                <h1>Dr. <?php echo htmlspecialchars($display_name); ?></h1>
                <p><?php echo $greeting; ?>. Here is your practice overview.</p>
            </div>
            <div style="background: rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 30px;"><i class="fas fa-calendar"></i> <?php echo date('F d, Y'); ?></div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
                <div>
                    <h3><?php echo $count_pat; ?></h3>
                    <p>Total Patients</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-green"><i class="fas fa-check-circle"></i></div>
                <div>
                    <h3><?php echo $count_done; ?></h3>
                    <p>Completed</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-orange"><i class="fas fa-clock"></i></div>
                <div>
                    <h3><?php echo $count_pend; ?></h3>
                    <p>Pending</p>
                </div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #ca8a04;">
                <div class="stat-icon icon-gold"><i class="fas fa-coins"></i></div>
                <div>
                    <h3>LKR <?php echo number_format($total_revenue, 2); ?></h3>
                    <p>Total Earnings</p>
                </div>
            </div>
        </div>

        <div class="forms-row">
            <div class="content-box">
                <h3 style="color: #0c5adb; margin-top:0;"><i class="fas fa-prescription-bottle-alt"></i> Write Prescription</h3>
                <form method="POST">
                    <div class="form-group"><label>Patient</label><select name="rx_patient_id" class="form-control" required>
                            <option value="">Select...</option><?php mysqli_data_seek($patients, 0);
                                                                while ($p = mysqli_fetch_assoc($patients)): ?><option value="<?= $p['id'] ?>"><?= $p['full_name'] ?></option><?php endwhile; ?>
                        </select></div>
                    <div class="form-group"><label>Diagnosis</label><input type="text" name="diagnosis" class="form-control" required></div>
                    <div class="form-group"><label>Medication</label><textarea name="medication" class="form-control" style="height:80px;" required></textarea></div>
                    <button type="submit" name="send_rx" class="btn-action btn-blue">Send Rx</button>
                </form>
            </div>
            <div class="content-box">
                <h3 style="color: #16a34a; margin-top:0;"><i class="fas fa-file-invoice-dollar"></i> Send Invoice</h3>
                <form method="POST">
                    <div class="form-group"><label>Confirmed Appointment</label><select name="bill_appt_id" class="form-control" required>
                            <option value="">Select...</option><?php mysqli_data_seek($appts, 0);
                                                                while ($a = mysqli_fetch_assoc($appts)): ?><option value="<?= $a['id'] . '_' . $a['patient_id'] ?>"><?= $a['full_name'] ?> (<?= $a['appointment_time'] ?>)</option><?php endwhile; ?>
                        </select></div>
                    <div class="form-group"><label>Service Description</label><input type="text" name="service_desc" class="form-control" required></div>
                    <div class="form-group"><label>Amount (LKR)</label><input type="number" name="amount" class="form-control" step="0.01" required></div>
                    <button type="submit" name="send_bill" class="btn-action btn-green">Send Bill</button>
                </form>
            </div>
        </div>

        <div class="content-box" style="border-top: 4px solid #16a34a;">
            <h3 style="margin-top:0; color:#333;">Recent Received Payments</h3>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Method</th>
                    <th>Amount</th>
                </tr>
                <?php while ($pay = mysqli_fetch_assoc($recent_payments)): ?>
                    <tr>
                        <td><?= date('M d, Y', strtotime($pay['paid_at'])) ?></td>
                        <td style="font-weight:600;"><?= $pay['full_name'] ?></td>
                        <td><?= $pay['payment_method'] ?? 'Online' ?></td>
                        <td style="color:#16a34a; font-weight:bold;">+ LKR <?= number_format($pay['amount'], 2) ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <div class="content-box">
            <h3 style="margin-top:0; color:#333;">Incoming Appointments</h3>
            <table>
                <tr>
                    <th>Time</th>
                    <th>Patient</th>
                    <th>Reason</th>
                    <th>Status</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($incoming_appts)): ?>
                    <tr>
                        <td><?= date('M d, h:i A', strtotime($row['appointment_time'])) ?></td>
                        <td><?= $row['full_name'] ?></td>
                        <td><?= $row['reason'] ?></td>
                        <td><span class="badge" style="background:#e0f2fe; color:#0284c7;"><?= $row['status'] ?></span></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>
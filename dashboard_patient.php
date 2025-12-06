<?php
session_start();
include 'db_connect.php';

// 1. SECURITY CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$patient_id = $_SESSION['user_id'];
$patient_name = $_SESSION['username'];

// --- HANDLE APPOINTMENT CANCELLATION ---
if (isset($_GET['cancel_id'])) {
    $cancel_id = (int)$_GET['cancel_id'];
    // Only allow deleting own appointments that are not completed
    mysqli_query($conn, "DELETE FROM appointments WHERE id='$cancel_id' AND patient_id='$patient_id' AND status != 'Completed'");
    header("Location: dashboard_patient.php");
    exit();
}

// Stats
$appts_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM appointments WHERE patient_id='$patient_id'");
$my_appts_count = mysqli_fetch_assoc($appts_query)['total'];

$rx_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM prescriptions WHERE patient_id='$patient_id'");
$my_rx_count = mysqli_fetch_assoc($rx_query)['total'];

$due_query = mysqli_query($conn, "SELECT SUM(amount) as total FROM invoices WHERE patient_id='$patient_id' AND status='unpaid'");
$total_due = mysqli_fetch_assoc($due_query)['total'] ?? 0;

// FETCH UPCOMING APPOINTMENTS
$upcoming_query = "SELECT a.*, d.name as doc_name 
                   FROM appointments a 
                   JOIN doctors d ON a.doctor_id = d.id 
                   WHERE a.patient_id='$patient_id' AND a.status != 'Completed' 
                   ORDER BY a.appointment_time ASC";
$res_upcoming = mysqli_query($conn, $upcoming_query);

// FETCH PRESCRIPTIONS
$presc_query = "SELECT p.*, d.name as doc_name 
                FROM prescriptions p 
                JOIN doctors d ON p.doctor_id = d.id 
                WHERE p.patient_id='$patient_id' 
                ORDER BY p.created_at DESC";
$res_presc = mysqli_query($conn, $presc_query);

// PAYMENT HISTORY
$paid_history = mysqli_query($conn, "SELECT * FROM payments WHERE patient_id='$patient_id' ORDER BY paid_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>My Dashboard</title>
    <?php include 'header.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        .dash-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .welcome-banner {
            background: linear-gradient(135deg, #0062cc, #0096ff);
            color: white;
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            flex-wrap: wrap;
            gap: 20px;
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

        .icon-purple {
            background: #f3e5f5;
            color: #6f42c1;
        }

        .icon-red {
            background: #fee2e2;
            color: #dc3545;
        }

        .content-box {
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .pay-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }

        .pay-table th {
            text-align: left;
            color: #888;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }

        .pay-table td {
            padding: 12px 0;
            border-bottom: 1px solid #f9f9f9;
            vertical-align: top;
        }

        .status-badge {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .Completed {
            background: #dcfce7;
            color: #16a34a;
        }

        .Confirmed {
            background: #e3f2fd;
            color: #0c5adb;
        }

        .Scheduled,
        .Pending {
            background: #fff3e0;
            color: #f59e0b;
        }

        .btn-cancel {
            color: #dc3545;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            background: #fee2e2;
            padding: 5px 10px;
            border-radius: 5px;
            transition: 0.3s;
        }

        .btn-cancel:hover {
            background: #dc3545;
            color: white;
        }
    </style>
</head>

<body>

    <div class="dash-container">
        <div class="welcome-banner">
            <div>
                <h1>Hello, <?= htmlspecialchars($patient_name) ?></h1>
                <p>Manage your health appointments and records.</p>
            </div>

            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="book_appointment.php" style="background:white; color:#0062cc; padding:10px 20px; border-radius:30px; text-decoration:none; font-weight:bold;">
                    + Book Appointment
                </a>

                <a href="inbox.php" style="background:rgba(255,255,255,0.2); border:1px solid white; color:white; padding:10px 20px; border-radius:30px; text-decoration:none; font-weight:bold;">
                    <i class="fas fa-comments"></i> Medical Chat
                </a>

                <a href="support_chat.php" style="background:rgba(255,255,255,0.2); border:1px solid white; color:white; padding:10px 20px; border-radius:30px; text-decoration:none; font-weight:bold;">
                    <i class="fas fa-headset"></i> Support
                </a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon icon-blue"><i class="fas fa-calendar-check"></i></div>
                <div>
                    <h3><?= $my_appts_count ?></h3>
                    <p>Appointments</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-purple"><i class="fas fa-file-prescription"></i></div>
                <div>
                    <h3><?= $my_rx_count ?></h3>
                    <p>Prescriptions</p>
                </div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #dc3545;">
                <div class="stat-icon icon-red"><i class="fas fa-file-invoice-dollar"></i></div>
                <div>
                    <h3 style="color:#dc3545;">LKR <?= number_format($total_due, 2) ?></h3>
                    <p style="color:#dc3545; font-weight:bold;">Total Due</p>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div class="content-box" style="border-top: 4px solid #dc3545;">
                <h3 style="margin-top:0; color:#dc3545;">Outstanding Bills</h3>
                <div id="live-bills-container">
                    <p style="color:#888;">Checking...</p>
                </div>
            </div>

            <div class="content-box" style="border-top: 4px solid #28a745;">
                <h3 style="margin-top:0;">Payment History</h3>
                <table class="pay-table">
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                    </tr>
                    <?php if (mysqli_num_rows($paid_history) > 0): ?>
                        <?php while ($h = mysqli_fetch_assoc($paid_history)): ?>
                            <tr>
                                <td><?= date('M d', strtotime($h['paid_at'])) ?></td>
                                <td style="color: #28a745; font-weight:bold;">LKR <?= number_format($h['amount'], 2) ?></td>
                                <td><?= $h['payment_method'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align:center; color:#999;">No payment history found.</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <div class="content-box">
            <h3 style="margin-top:0;">Upcoming Appointments</h3>
            <table class="pay-table">
                <tr>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php if (mysqli_num_rows($res_upcoming) > 0): ?>
                    <?php while ($a = mysqli_fetch_assoc($res_upcoming)):
                        $clean_name = trim(str_ireplace(["Dr.", "Dr "], "", $a['doc_name']));
                    ?>
                        <tr>
                            <td><strong>Dr. <?= htmlspecialchars($clean_name) ?></strong></td>
                            <td><?= date('M d, Y - h:i A', strtotime($a['appointment_time'])) ?></td>
                            <td><span class="status-badge <?= $a['status'] ?>"><?= $a['status'] ?></span></td>
                            <td>
                                <a href="?cancel_id=<?= $a['id'] ?>" class="btn-cancel" onclick="return confirm('Cancel this appointment?');">Cancel</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center; color:#999;">No upcoming appointments.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>

        <div class="content-box" style="border-top: 4px solid #6f42c1;">
            <h3 style="margin-top:0; color:#6f42c1;">My Prescriptions</h3>
            <table class="pay-table">
                <tr>
                    <th style="width: 15%;">Date</th>
                    <th style="width: 20%;">Doctor</th>
                    <th style="width: 20%;">Diagnosis</th>
                    <th style="width: 45%;">Medicines & Dosage</th>
                </tr>
                <?php if (mysqli_num_rows($res_presc) > 0): ?>
                    <?php while ($rx = mysqli_fetch_assoc($res_presc)):
                        $doc_name = trim(str_ireplace(["Dr.", "Dr "], "", $rx['doc_name']));
                    ?>
                        <tr>
                            <td><?= date('M d, Y', strtotime($rx['created_at'])) ?></td>
                            <td><strong>Dr. <?= htmlspecialchars($doc_name) ?></strong></td>
                            <td><?= htmlspecialchars($rx['diagnosis']) ?></td>
                            <td style="line-height: 1.6;">
                                <?= nl2br(htmlspecialchars($rx['dosage_instructions'] ?? $rx['medicine_list'])) ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center; color:#999; padding: 20px;">No prescriptions found.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>

    </div>

    <script>
        $(document).ready(function() {
            $("#live-bills-container").load("load_bills.php");
            setInterval(function() {
                $("#live-bills-container").load("load_bills.php");
            }, 3000);
        });
    </script>

    <?php include 'footer.php'; ?>
</body>

</html>
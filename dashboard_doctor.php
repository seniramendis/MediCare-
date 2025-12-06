<?php
session_start();
include 'db_connect.php';
date_default_timezone_set('Asia/Colombo');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['user_id'];
$doctor_name = $_SESSION['username'];
$display_name = trim(str_ireplace("Dr.", "", $doctor_name));

// --- PHP ACTIONS ---
if (isset($_POST['send_rx'])) {
    $p_id = $_POST['rx_patient_id'];
    $diag = mysqli_real_escape_string($conn, $_POST['diagnosis']);
    $meds = mysqli_real_escape_string($conn, $_POST['medication']);
    $sql = "INSERT INTO prescriptions (doctor_id, patient_id, diagnosis, medicine_list, dosage_instructions) VALUES ('$doctor_id', '$p_id', '$diag', 'General Rx', '$meds')";
    mysqli_query($conn, $sql);
}

if (isset($_POST['send_bill'])) {
    $appt_val = $_POST['bill_appt_id'];
    list($appt_id, $pat_id) = explode('_', $appt_val);
    $service = mysqli_real_escape_string($conn, $_POST['service_desc']);
    $amount = $_POST['amount'];

    $check = mysqli_query($conn, "SELECT COUNT(*) as count FROM invoices WHERE appointment_id='$appt_id'");
    if (mysqli_fetch_assoc($check)['count'] < 2) {
        // FIX: Changed 'service_name' to 'service_description' to match your DB
        $sql = "INSERT INTO invoices (patient_id, doctor_id, appointment_id, amount, service_description, status) VALUES ('$pat_id', '$doctor_id', '$appt_id', '$amount', '$service', 'unpaid')";
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('✅ Invoice sent!');</script>";
        } else {
            // Added Error Reporting so you can see if it fails
            echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('⚠️ Limit reached or Invoice already sent');</script>";
    }
}

$my_patients = mysqli_query($conn, "SELECT DISTINCT u.id, u.full_name FROM users u JOIN appointments a ON u.id = a.patient_id WHERE a.doctor_id='$doctor_id'");

$confirmed_appts = mysqli_query($conn, "SELECT a.id, a.patient_id, a.appointment_time, u.full_name 
                                        FROM appointments a 
                                        JOIN users u ON a.patient_id = u.id 
                                        WHERE a.doctor_id='$doctor_id' AND a.status='Confirmed'");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Doctor Dashboard</title>
    <?php include 'header.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --primary-color: #0c5adb;
            --bg-overlay: rgba(255, 255, 255, 0.9);
        }

        body {
            background: linear-gradient(var(--bg-overlay), var(--bg-overlay)), url('images/background3.png');
            background-size: cover;
            background-attachment: fixed;
            font-family: 'Poppins', sans-serif;
        }

        .dash-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: #e0f2fe;
            color: #0c5adb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .content-box {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .forms-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        .btn-action {
            width: 100%;
            padding: 10px;
            background: #0c5adb;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            text-align: left;
            padding: 10px;
            color: #888;
            font-size: 13px;
            border-bottom: 2px solid #f5f5f5;
        }

        td {
            padding: 12px 10px;
            border-bottom: 1px solid #f5f5f5;
            font-size: 14px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .st-Confirmed {
            background: #e3f2fd;
            color: #0062cc;
        }

        .st-Pending {
            background: #fff3e0;
            color: #ff9800;
        }

        .st-Completed {
            background: #dcfce7;
            color: #16a34a;
        }

        .btn-icon {
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            margin-right: 5px;
            font-size: 12px;
            font-weight: bold;
        }

        .btn-accept {
            background: #16a34a;
            color: white;
        }

        .btn-check {
            background: #0c5adb;
            color: white;
        }

        .btn-trash {
            background: #ffebee;
            color: #c62828;
        }
    </style>
</head>

<body>

    <div class="dash-container">
        <div style="background: #0c5adb; color: white; padding: 30px; border-radius: 15px; margin-bottom: 30px;">
            <h1>Dr. <?= htmlspecialchars($display_name) ?></h1>
            <p>Live Dashboard Overview</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div>
                    <h3 id="cnt-total">0</h3>
                    <p>Patients</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div>
                    <h3 id="cnt-completed">0</h3>
                    <p>Completed</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div>
                    <h3 id="cnt-pending">0</h3>
                    <p>Pending</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-wallet"></i></div>
                <div>
                    <h3>LKR <span id="live-revenue">0.00</span></h3>
                    <p>Earnings</p>
                </div>
            </div>
        </div>

        <div class="forms-row">
            <div class="content-box">
                <h3>Write Prescription</h3>
                <form method="POST">
                    <select name="rx_patient_id" class="form-control" required>
                        <option value="">Select Patient...</option>
                        <?php mysqli_data_seek($my_patients, 0);
                        while ($p = mysqli_fetch_assoc($my_patients)): ?>
                            <option value="<?= $p['id'] ?>"><?= $p['full_name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                    <input type="text" name="diagnosis" class="form-control" placeholder="Diagnosis" required>
                    <textarea name="medication" class="form-control" placeholder="Medication Details..." required></textarea>
                    <button type="submit" name="send_rx" class="btn-action">Send Rx</button>
                </form>
            </div>

            <div class="content-box">
                <h3>Send Invoice</h3>
                <p style="font-size:12px; color:#666;">Note: You must 'Accept' an appointment below to enable billing.</p>
                <form method="POST">
                    <select name="bill_appt_id" class="form-control" required>
                        <option value="" disabled selected>Select Confirmed Appointment...</option>
                        <?php if (mysqli_num_rows($confirmed_appts) > 0): ?>
                            <?php mysqli_data_seek($confirmed_appts, 0);
                            while ($a = mysqli_fetch_assoc($confirmed_appts)): ?>
                                <option value="<?= $a['id'] . '_' . $a['patient_id'] ?>"><?= $a['full_name'] ?> (<?= $a['appointment_time'] ?>)</option>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <option disabled>No confirmed appointments</option>
                        <?php endif; ?>
                    </select>
                    <input type="text" name="service_desc" class="form-control" placeholder="Service (e.g. Checkup)" required>
                    <input type="number" name="amount" class="form-control" placeholder="Amount (LKR)" required>
                    <button type="submit" name="send_bill" class="btn-action">Send Bill</button>
                </form>
            </div>
        </div>

        <div class="content-box">
            <h3>Incoming Appointments</h3>
            <table>
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="upcomingTableBody">
                    <tr>
                        <td colspan="5">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="content-box">
            <h3>History (Completed)</h3>
            <table>
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody"></tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            fetchAppointments();
            fetchStats();
            setInterval(fetchAppointments, 3000);
            setInterval(fetchStats, 3000);
        });

        function fetchStats() {
            $.getJSON('fetch_earnings.php', function(data) {
                $('#live-revenue').text(data.revenue);
                $('#cnt-total').text(data.total);
                $('#cnt-completed').text(data.completed);
                $('#cnt-pending').text(data.pending);
            });
        }

        function fetchAppointments() {
            $.post('doctor_api.php', {
                action: 'fetch'
            }, function(data) {
                let upcomingRows = '',
                    historyRows = '';

                if (data.length === 0) {
                    upcomingRows = '<tr><td colspan="5" style="padding:20px; text-align:center; color:#999;">No appointments found.</td></tr>';
                } else {
                    data.forEach(function(appt) {
                        let status = appt.status.trim().toLowerCase();
                        let displayTime = appt.formatted_time;

                        if (status === 'completed') {
                            historyRows += `<tr>
                            <td>${displayTime}</td>
                            <td>${appt.patient_name}</td>
                            <td>${appt.reason}</td>
                            <td><span class="status-badge st-Completed">Completed</span></td>
                            <td><button class="btn-icon btn-trash" onclick="deleteAppt(${appt.id})"><i class="fas fa-trash"></i></button></td>
                        </tr>`;
                        } else {
                            let buttons = '';
                            let badgeClass = (status === 'confirmed') ? 'st-Confirmed' : 'st-Pending';

                            if (status === 'confirmed') {
                                buttons = `<button class="btn-icon btn-check" onclick="updateAppt(${appt.id}, 'Completed')"><i class="fas fa-check-double"></i> Complete</button>`;
                            } else {
                                buttons = `<button class="btn-icon btn-accept" onclick="updateAppt(${appt.id}, 'Confirmed')"><i class="fas fa-check"></i> Accept</button>
                                       <button class="btn-icon btn-trash" onclick="deleteAppt(${appt.id})"><i class="fas fa-trash"></i></button>`;
                            }

                            upcomingRows += `<tr>
                            <td>${displayTime}</td>
                            <td>${appt.patient_name}</td>
                            <td>${appt.reason}</td>
                            <td><span class="status-badge ${badgeClass}">${appt.status}</span></td>
                            <td>${buttons}</td>
                        </tr>`;
                        }
                    });
                }
                $('#upcomingTableBody').html(upcomingRows);
                $('#historyTableBody').html(historyRows);
            }, 'json');
        }

        function updateAppt(id, status) {
            $.post('doctor_api.php', {
                action: 'update',
                id: id,
                status: status
            }, function() {
                if (status === 'Confirmed') {
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                } else {
                    fetchAppointments();
                }
            });
        }

        function deleteAppt(id) {
            if (confirm('Delete this appointment?')) {
                $.post('doctor_api.php', {
                    action: 'delete',
                    id: id
                }, function() {
                    fetchAppointments();
                });
            }
        }
    </script>

</body>

</html>
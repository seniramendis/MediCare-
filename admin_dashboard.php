<?php
session_start();
include 'db_connect.php';

// --- CONFIGURATION ---
$doctor_table = "doctors";
// ---------------------

// 1. AUTHENTICATION
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

$message = "";
$msg_type = "";
$view = isset($_GET['view']) ? $_GET['view'] : 'dashboard';
$edit_mode = isset($_GET['edit']);
$edit_id = isset($_GET['id']) ? $_GET['id'] : null;

// --- 2. HANDLE SAVING DATA (POST) ---

// SAVE DOCTOR (Updated with Email & Password)
if (isset($_POST['save_doctor'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $specialty = mysqli_real_escape_string($conn, $_POST['specialty']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if ID exists (Update vs Insert)
    if (empty($_POST['doc_id'])) {
        // --- INSERT NEW DOCTOR ---
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Check for duplicate email first
        $check = mysqli_query($conn, "SELECT id FROM $doctor_table WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $message = "Error: Email already exists!";
            $msg_type = "error";
        } else {
            $sql = "INSERT INTO $doctor_table (name, specialty, email, password) VALUES ('$name', '$specialty', '$email', '$password')";
            if (mysqli_query($conn, $sql)) {
                $message = "Doctor added successfully with login access!";
                $msg_type = "success";
            } else {
                $message = "Error: " . mysqli_error($conn);
                $msg_type = "error";
            }
        }
    } else {
        // --- UPDATE EXISTING DOCTOR ---
        $uid = $_POST['doc_id'];

        // Handle Password Update (Only if filled)
        $pass_sql = "";
        if (!empty($_POST['password'])) {
            $p = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $pass_sql = ", password='$p'";
        }

        $sql = "UPDATE $doctor_table SET name='$name', specialty='$specialty', email='$email' $pass_sql WHERE id='$uid'";
        if (mysqli_query($conn, $sql)) {
            echo "<script>window.location.href='admin_dashboard.php?view=doctors';</script>";
        } else {
            $message = "Error: " . mysqli_error($conn);
            $msg_type = "error";
        }
    }
}

// SAVE PATIENT
if (isset($_POST['save_patient'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $dob = $_POST['dob'];

    if (empty($_POST['user_id'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $message = "Email already exists!";
            $msg_type = "error";
        } else {
            $sql = "INSERT INTO users (full_name, email, password, role, address, dob) 
                    VALUES ('$name', '$email', '$password', 'patient', '$address', '$dob')";
            if (mysqli_query($conn, $sql)) {
                $message = "Patient added!";
                $msg_type = "success";
            }
        }
    } else {
        $uid = $_POST['user_id'];
        $pass_sql = "";
        if (!empty($_POST['password'])) {
            $p = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $pass_sql = ", password='$p'";
        }
        $sql = "UPDATE users SET full_name='$name', email='$email', address='$address', dob='$dob' $pass_sql WHERE id='$uid'";
        if (mysqli_query($conn, $sql)) {
            echo "<script>window.location.href='admin_dashboard.php?view=patients';</script>";
        }
    }
}

// SAVE APPOINTMENT
if (isset($_POST['save_appointment'])) {
    $pat_id = $_POST['patient_id'];
    $doc_id = $_POST['doctor_id'];
    $time = $_POST['appointment_time'];
    $status = $_POST['status'];
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);

    if (empty($_POST['app_id'])) {
        $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_time, status, reason) VALUES ('$pat_id', '$doc_id', '$time', '$status', '$reason')";
        if (mysqli_query($conn, $sql)) {
            $message = "Appointment booked!";
            $msg_type = "success";
        } else {
            $message = "SQL Error: " . mysqli_error($conn);
            $msg_type = "error";
        }
    } else {
        $aid = $_POST['app_id'];
        $sql = "UPDATE appointments SET patient_id='$pat_id', doctor_id='$doc_id', appointment_time='$time', status='$status', reason='$reason' WHERE id='$aid'";
        if (mysqli_query($conn, $sql)) {
            echo "<script>window.location.href='admin_dashboard.php?view=appointments';</script>";
        }
    }
}

// --- 3. HANDLE DELETION ---
if (isset($_GET['delete']) && isset($_GET['id']) && isset($_GET['type'])) {
    $id = $_GET['id'];
    $type = $_GET['type'];

    if ($type == 'doctor') {
        mysqli_query($conn, "DELETE FROM $doctor_table WHERE id='$id'");
        header("Location: admin_dashboard.php?view=doctors");
    } elseif ($type == 'patient') {
        mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
        header("Location: admin_dashboard.php?view=patients");
    } elseif ($type == 'appointment') {
        mysqli_query($conn, "DELETE FROM appointments WHERE id='$id'");
        header("Location: admin_dashboard.php?view=appointments");
    }
}

// --- 4. HELPERS ---
function getPatient($conn, $id)
{
    return mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$id'"));
}
function getDoctor($conn, $id, $table)
{
    return mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM $table WHERE id='$id'"));
}
function getAppt($conn, $id)
{
    return mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM appointments WHERE id='$id'"));
}

$all_doctors = @mysqli_query($conn, "SELECT * FROM $doctor_table");
$all_patients = mysqli_query($conn, "SELECT * FROM users WHERE role='patient'");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://kit.fontawesome.com/9e166a3863.js" crossorigin="anonymous"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --sidebar-bg: #0f172a;
            --active-item: #2563eb;
            --bg-color: #f1f5f9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            background: var(--bg-color);
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            color: white;
            padding: 25px;
            position: fixed;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .menu-item {
            padding: 12px;
            color: #94a3b8;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: 0.3s;
        }

        .menu-item:hover,
        .menu-item.active {
            background: var(--active-item);
            color: white;
        }

        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 30px;
        }

        .panel {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 12px;
            background: #f8fafc;
            color: #64748b;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        td {
            padding: 15px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
            color: #334155;
        }

        .btn-add {
            background: #2563eb;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 20px;
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            outline: none;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .stat-num {
            font-size: 24px;
            font-weight: bold;
            color: #1e293b;
        }

        .alert {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h2 style="margin-bottom: 40px;"><i class="fas fa-heartbeat"></i> AdminPanel</h2>
        <a href="?view=dashboard" class="menu-item <?php echo $view == 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Dashboard</a>
        <a href="?view=doctors" class="menu-item <?php echo $view == 'doctors' ? 'active' : ''; ?>"><i class="fas fa-user-md"></i> Doctors</a>
        <a href="?view=patients" class="menu-item <?php echo $view == 'patients' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Patients</a>
        <a href="?view=appointments" class="menu-item <?php echo $view == 'appointments' ? 'active' : ''; ?>"><i class="fas fa-calendar-alt"></i> Appointments</a>
        <a href="logout.php" class="menu-item" style="margin-top:auto; color:#ef4444;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <?php if ($message): ?><div class="alert <?php echo $msg_type; ?>"><?php echo $message; ?></div><?php endif; ?>

        <?php if ($view == 'dashboard'): ?>
            <h2>Overview</h2><br>
            <div class="stats-grid">
                <?php
                $doc_check = @mysqli_query($conn, "SELECT COUNT(*) as c FROM $doctor_table");
                $doc_count = $doc_check ? mysqli_fetch_assoc($doc_check)['c'] : "DB Error";
                ?>
                <div class="stat-card">
                    <div class="stat-num"><?php echo $doc_count; ?></div>Doctors
                </div>
                <div class="stat-card">
                    <div class="stat-num"><?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role='patient'"))['c']; ?></div>Patients
                </div>
                <div class="stat-card">
                    <div class="stat-num"><?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM appointments"))['c']; ?></div>Appointments
                </div>
            </div>
        <?php endif; ?>

        <?php if ($view == 'doctors'): ?>
            <div style="display:flex; justify-content:space-between;">
                <h2>Manage Doctors</h2>
                <?php if (!$edit_mode): ?><a href="?view=doctors&edit=1" class="btn-add">+ Add Doctor</a><?php endif; ?>
            </div>

            <?php if ($edit_mode): $data = $edit_id ? getDoctor($conn, $edit_id, $doctor_table) : null; ?>
                <div class="form-container">
                    <h3><?php echo $edit_id ? 'Edit Doctor' : 'Add New Doctor'; ?></h3><br>
                    <form method="POST">
                        <input type="hidden" name="doc_id" value="<?php echo $data['id'] ?? ''; ?>">
                        <div class="form-group"><label>Name</label><input type="text" name="name" value="<?php echo $data['name'] ?? ''; ?>" required></div>
                        <div class="form-group"><label>Specialty</label><input type="text" name="specialty" value="<?php echo $data['specialty'] ?? ''; ?>" required></div>

                        <!-- NEW FIELDS FOR LOGIN -->
                        <div class="form-group"><label>Email (Login Username)</label><input type="email" name="email" value="<?php echo $data['email'] ?? ''; ?>" required></div>
                        <div class="form-group"><label>Password</label><input type="password" name="password" <?php echo $edit_id ? '' : 'required'; ?> placeholder="<?php echo $edit_id ? 'Leave blank to keep current' : ''; ?>"></div>

                        <button type="submit" name="save_doctor" class="btn-submit">Save Doctor</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="panel">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Specialty</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res = @mysqli_query($conn, "SELECT * FROM $doctor_table");
                            if ($res) {
                                while ($row = mysqli_fetch_assoc($res)): ?>
                                    <tr>
                                        <td>Dr. <?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['specialty']); ?></td>
                                        <td><?php echo isset($row['email']) ? htmlspecialchars($row['email']) : '<small style="color:red">No Email</small>'; ?></td>
                                        <td>
                                            <a href="?view=doctors&edit=1&id=<?php echo $row['id']; ?>" style="color:#2563eb;">Edit</a>
                                            <a href="?delete=1&type=doctor&id=<?php echo $row['id']; ?>" style="color:#ef4444; margin-left:10px;" onclick="return confirm('Delete?');">Delete</a>
                                        </td>
                                    </tr>
                            <?php endwhile;
                            } ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Patients View (Collapsed for brevity - assumes same logic as previous) -->
        <?php if ($view == 'patients'): ?>
            <!-- ... (Use patient logic from previous admin_dashboard file, no changes needed here) ... -->
            <!-- For completeness, just paste the Patient section from your previous working admin_dashboard.php here -->
            <div style="display:flex; justify-content:space-between;">
                <h2>Manage Patients</h2><?php if (!$edit_mode): ?><a href="?view=patients&edit=1" class="btn-add">+ Add Patient</a><?php endif; ?>
            </div>
            <?php if ($edit_mode): $data = $edit_id ? getPatient($conn, $edit_id) : null; ?>
                <div class="form-container">
                    <form method="POST">
                        <input type="hidden" name="user_id" value="<?php echo $data['id'] ?? ''; ?>">
                        <div class="form-group"><label>Full Name</label><input type="text" name="name" value="<?php echo $data['full_name'] ?? ''; ?>" required></div>
                        <div class="form-group"><label>Email</label><input type="email" name="email" value="<?php echo $data['email'] ?? ''; ?>" required></div>
                        <div class="form-group"><label>Password</label><input type="password" name="password" <?php echo $edit_id ? '' : 'required'; ?>></div>
                        <button type="submit" name="save_patient" class="btn-submit">Save Patient</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="panel">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody><?php $res = mysqli_query($conn, "SELECT * FROM users WHERE role='patient'");
                                while ($row = mysqli_fetch_assoc($res)): ?><tr>
                                    <td><?php echo $row['full_name']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><a href="?view=patients&edit=1&id=<?php echo $row['id']; ?>">Edit</a></td>
                                </tr><?php endwhile; ?></tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Appointments View -->
        <?php if ($view == 'appointments'): ?>
            <div style="display:flex; justify-content:space-between;">
                <h2>Manage Appointments</h2><?php if (!$edit_mode): ?><a href="?view=appointments&edit=1" class="btn-add">+ Book New</a><?php endif; ?>
            </div>
            <?php if ($edit_mode): $data = $edit_id ? getAppt($conn, $edit_id) : null; ?>
                <div class="form-container">
                    <form method="POST">
                        <!-- Simplified form for brevity, ensure full form is used in production -->
                        <input type="hidden" name="app_id" value="<?php echo $data['id'] ?? ''; ?>">
                        <div class="form-group"><label>Patient ID</label><input type="text" name="patient_id" value="<?php echo $data['patient_id'] ?? ''; ?>" required></div>
                        <div class="form-group"><label>Doctor ID</label><input type="text" name="doctor_id" value="<?php echo $data['doctor_id'] ?? ''; ?>" required></div>
                        <div class="form-group"><label>Time</label><input type="datetime-local" name="appointment_time" value="<?php echo $data['appointment_time'] ?? ''; ?>" required></div>
                        <div class="form-group"><label>Status</label><select name="status">
                                <option>Pending</option>
                                <option>Confirmed</option>
                            </select></div>
                        <button type="submit" name="save_appointment" class="btn-submit">Save</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="panel">
                    <table>
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody><?php $res = mysqli_query($conn, "SELECT * FROM appointments ORDER BY appointment_time DESC");
                                while ($row = mysqli_fetch_assoc($res)): ?><tr>
                                    <td><?php echo $row['patient_id']; ?></td>
                                    <td><?php echo $row['doctor_id']; ?></td>
                                    <td><?php echo $row['appointment_time']; ?></td>
                                    <td><?php echo $row['status']; ?></td>
                                </tr><?php endwhile; ?></tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>

</html>
<?php
session_start();
include 'db_connect.php';


$doctor_table = "doctors";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

$message = "";
$msg_type = "";
$view = isset($_GET['view']) ? $_GET['view'] : 'dashboard';
$edit_mode = isset($_GET['edit']);
$edit_id = isset($_GET['id']) ? $_GET['id'] : null;


if (isset($_POST['save_doctor'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $specialty = mysqli_real_escape_string($conn, $_POST['specialty']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);


    $image_sql_part = "";
    $final_image_path = "images/doctor.png";

    if (isset($_FILES['doc_image']) && $_FILES['doc_image']['error'] == 0) {
        $target_dir = "images/";
        $file_ext = strtolower(pathinfo($_FILES["doc_image"]["name"], PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($file_ext, $allowed_types)) {
            $new_filename = "doc_" . time() . "_" . rand(1000, 9999) . "." . $file_ext;
            $target_file = $target_dir . $new_filename;

            if (move_uploaded_file($_FILES["doc_image"]["tmp_name"], $target_file)) {
                $image_sql_part = ", image='$target_file'";
                $final_image_path = $target_file;
            } else {
                $message = "Error moving uploaded file.";
                $msg_type = "error";
            }
        } else {
            $message = "Invalid file type. Only JPG, PNG, GIF allowed.";
            $msg_type = "error";
        }
    }

    if (empty($_POST['doc_id'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $check = mysqli_query($conn, "SELECT id FROM $doctor_table WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $message = "Error: Email already exists!";
            $msg_type = "error";
        } else {
            $sql = "INSERT INTO $doctor_table (name, specialty, email, password, image) 
                    VALUES ('$name', '$specialty', '$email', '$password', '$final_image_path')";
            if (mysqli_query($conn, $sql)) {
                $message = "Doctor added successfully!";
                $msg_type = "success";
            } else {
                $message = "Error: " . mysqli_error($conn);
                $msg_type = "error";
            }
        }
    } else {
        $uid = $_POST['doc_id'];
        $pass_sql = "";
        if (!empty($_POST['password'])) {
            $p = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $pass_sql = ", password='$p'";
        }
        $sql = "UPDATE $doctor_table SET name='$name', specialty='$specialty', email='$email' $pass_sql $image_sql_part WHERE id='$uid'";
        if (mysqli_query($conn, $sql)) {
            echo "<script>window.location.href='admin_dashboard.php?view=doctors';</script>";
        } else {
            $message = "Error: " . mysqli_error($conn);
            $msg_type = "error";
        }
    }
}


if (isset($_POST['save_patient'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = isset($_POST['address']) ? mysqli_real_escape_string($conn, $_POST['address']) : '';
    $dob = isset($_POST['dob']) ? $_POST['dob'] : '';

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
        $sql = "UPDATE users SET full_name='$name', email='$email' $pass_sql WHERE id='$uid'";
        if (mysqli_query($conn, $sql)) {
            echo "<script>window.location.href='admin_dashboard.php?view=patients';</script>";
        }
    }
}


if (isset($_POST['save_appointment'])) {
    $pat_id = $_POST['patient_id'];
    $doc_id = $_POST['doctor_id'];
    $time = $_POST['appointment_time'];
    $status = $_POST['status'];
    $reason = isset($_POST['reason']) ? mysqli_real_escape_string($conn, $_POST['reason']) : '';

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
    exit();
}


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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect width=%22100%22 height=%22100%22 rx=%2220%22 fill=%22%230c5adb%22/><path fill=%22%23ffffff%22 d=%22M35 20h30v25h25v30h-25v25h-30v-25h-25v-30h25z%22/></svg>">

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
            vertical-align: middle;
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

        input[type="file"] {
            padding: 8px;
            background: #f8fafc;
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
            grid-template-columns: repeat(4, 1fr);
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

        .dashboard-widgets {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .action-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
        }

        .action-btn {
            background: white;
            border: 1px solid #e2e8f0;
            padding: 15px;
            text-align: center;
            border-radius: 10px;
            text-decoration: none;
            color: #475569;
            font-size: 13px;
            font-weight: 600;
            transition: 0.3s;
        }

        .action-btn:hover {
            background: #eff6ff;
            border-color: #2563eb;
            color: #2563eb;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-pending {
            background: #fff7ed;
            color: #c2410c;
        }

        .status-confirmed {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .status-completed {
            background: #f0fdf4;
            color: #15803d;
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

        .doc-thumb {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h2 style="margin-bottom: 40px; padding-left:10px;"><i class="fas fa-heartbeat"></i> AdminPanel</h2>
        <a href="?view=dashboard" class="menu-item <?php echo $view == 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Dashboard</a>
        <a href="?view=doctors" class="menu-item <?php echo $view == 'doctors' ? 'active' : ''; ?>"><i class="fas fa-user-md"></i> Doctors</a>
        <a href="?view=patients" class="menu-item <?php echo $view == 'patients' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Patients</a>
        <a href="?view=appointments" class="menu-item <?php echo $view == 'appointments' ? 'active' : ''; ?>"><i class="fas fa-calendar-alt"></i> Appointments</a>

        <a href="admin_chat.php" class="menu-item"><i class="fas fa-comments"></i> Patient Support</a>
        <a href="admin_doctor_chat.php" class="menu-item"><i class="fas fa-user-md"></i> Doctor Support</a>

        <a href="logout.php" class="menu-item" style="margin-top:auto; color:#ef4444;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <?php if ($message): ?><div class="alert <?php echo $msg_type; ?>"><?php echo $message; ?></div><?php endif; ?>

        <?php if ($view == 'dashboard'): ?>
            <h2 style="margin-bottom:20px;">Dashboard Overview</h2>

            <div class="stats-grid">
                <?php
                $doc_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM $doctor_table"))['c'];
                $pat_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role='patient'"))['c'];
                $app_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM appointments"))['c'];
                $rev_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as c FROM payments"))['c'] ?? 0;
                ?>
                <div class="stat-card">
                    <div style="color:#2563eb; margin-bottom:5px;"><i class="fas fa-user-md fa-2x"></i></div>
                    <div class="stat-num"><?php echo $doc_count; ?></div>
                    <div style="color:#64748b; font-size:13px;">Total Doctors</div>
                </div>
                <div class="stat-card">
                    <div style="color:#16a34a; margin-bottom:5px;"><i class="fas fa-users fa-2x"></i></div>
                    <div class="stat-num"><?php echo $pat_count; ?></div>
                    <div style="color:#64748b; font-size:13px;">Total Patients</div>
                </div>
                <div class="stat-card">
                    <div style="color:#f59e0b; margin-bottom:5px;"><i class="fas fa-calendar-check fa-2x"></i></div>
                    <div class="stat-num"><?php echo $app_count; ?></div>
                    <div style="color:#64748b; font-size:13px;">Appointments</div>
                </div>
                <div class="stat-card">
                    <div style="color:#dc2626; margin-bottom:5px;"><i class="fas fa-wallet fa-2x"></i></div>
                    <div class="stat-num">LKR <?php echo number_format($rev_total); ?></div>
                    <div style="color:#64748b; font-size:13px;">Total Revenue</div>
                </div>
            </div>

            <div class="dashboard-widgets">
                <div class="panel">
                    <h3 style="margin-bottom:15px; color:#1e293b;">Recent Appointments</h3>
                    <table style="font-size:13px;">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $rec_appt = mysqli_query($conn, "SELECT a.*, u.full_name as pname, d.name as dname FROM appointments a JOIN users u ON a.patient_id = u.id JOIN doctors d ON a.doctor_id = d.id ORDER BY a.appointment_time DESC LIMIT 5");
                            if (mysqli_num_rows($rec_appt) > 0):
                                while ($r = mysqli_fetch_assoc($rec_appt)):
                                    $st_class = 'status-pending';
                                    if (strtolower($r['status']) == 'confirmed') $st_class = 'status-confirmed';
                                    if (strtolower($r['status']) == 'completed') $st_class = 'status-completed';
                            ?>
                                    <tr>
                                        <td><?php echo $r['pname']; ?></td>
                                        <td>Dr. <?php echo $r['dname']; ?></td>
                                        <td><?php echo date('M d, h:i A', strtotime($r['appointment_time'])); ?></td>
                                        <td><span class="status-badge <?php echo $st_class; ?>"><?php echo $r['status']; ?></span></td>
                                    </tr>
                                <?php endwhile;
                            else: ?>
                                <tr>
                                    <td colspan="4" style="text-align:center; color:#999;">No recent appointments</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div style="display:flex; flex-direction:column; gap:20px;">
                    <div class="panel">
                        <h3 style="margin-bottom:10px; color:#1e293b;">Quick Actions</h3>
                        <div class="action-grid">
                            <a href="?view=doctors&edit=1" class="action-btn"><i class="fas fa-user-plus"></i> Add Doctor</a>
                            <a href="?view=patients&edit=1" class="action-btn"><i class="fas fa-user-injured"></i> Add Patient</a>
                            <a href="admin_chat.php" class="action-btn"><i class="fas fa-comments"></i> Support</a>
                            <a href="admin_doctor_chat.php" class="action-btn"><i class="fas fa-user-md"></i> Dr. Chat</a>
                        </div>
                    </div>
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
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="doc_id" value="<?php echo $data['id'] ?? ''; ?>">
                        <div class="form-group"><label>Name</label><input type="text" name="name" value="<?php echo $data['name'] ?? ''; ?>" required></div>
                        <div class="form-group"><label>Specialty</label><input type="text" name="specialty" value="<?php echo $data['specialty'] ?? ''; ?>" required></div>
                        <div class="form-group"><label>Email</label><input type="email" name="email" value="<?php echo $data['email'] ?? ''; ?>" required></div>
                        <div class="form-group"><label>Password</label><input type="password" name="password" <?php echo $edit_id ? '' : 'required'; ?>></div>
                        <div class="form-group"><label>Profile Image</label><input type="file" name="doc_image" accept="image/*"></div>
                        <button type="submit" name="save_doctor" class="btn-submit">Save Doctor</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="panel">
                    <table>
                        <thead>
                            <tr>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Specialty</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $res = @mysqli_query($conn, "SELECT * FROM $doctor_table");
                            while ($row = mysqli_fetch_assoc($res)):
                                $img = !empty($row['image']) ? $row['image'] : 'images/doctor.png'; ?>
                                <tr>
                                    <td><img src="<?php echo htmlspecialchars($img); ?>" class="doc-thumb"></td>
                                    <td>Dr. <?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['specialty']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td>
                                        <a href="?view=doctors&edit=1&id=<?php echo $row['id']; ?>" style="color:#2563eb;">Edit</a>
                                        <a href="?delete=1&type=doctor&id=<?php echo $row['id']; ?>" style="color:#ef4444; margin-left:10px;" onclick="return confirm('Delete?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($view == 'patients'): ?>
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
                                    <td><a href="?view=patients&edit=1&id=<?php echo $row['id']; ?>">Edit</a> <a href="?delete=1&type=patient&id=<?php echo $row['id']; ?>" style="color:red; margin-left:10px;" onclick="return confirm('Delete?');">Delete</a></td>
                                </tr><?php endwhile; ?></tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($view == 'appointments'): ?>
            <div style="display:flex; justify-content:space-between;">
                <h2>Manage Appointments</h2><?php if (!$edit_mode): ?><a href="?view=appointments&edit=1" class="btn-add">+ Book New</a><?php endif; ?>
            </div>
            <?php if ($edit_mode): $data = $edit_id ? getAppt($conn, $edit_id) : null; ?>
                <div class="form-container">
                    <form method="POST">
                        <input type="hidden" name="app_id" value="<?php echo $data['id'] ?? ''; ?>">
                        <div class="form-group"><label>Patient ID</label><input type="text" name="patient_id" value="<?php echo $data['patient_id'] ?? ''; ?>" required></div>
                        <div class="form-group"><label>Doctor ID</label><input type="text" name="doctor_id" value="<?php echo $data['doctor_id'] ?? ''; ?>" required></div>
                        <div class="form-group"><label>Time</label><input type="datetime-local" name="appointment_time" value="<?php echo $data['appointment_time'] ?? ''; ?>" required></div>
                        <div class="form-group"><label>Status</label><select name="status">
                                <option>Pending</option>
                                <option>Confirmed</option>
                                <option>Completed</option>
                            </select></div>
                        <button type="submit" name="save_appointment" class="btn-submit">Save</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="panel">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody><?php $res = mysqli_query($conn, "SELECT * FROM appointments ORDER BY appointment_time DESC");
                                while ($row = mysqli_fetch_assoc($res)): ?><tr>
                                    <td>#<?php echo $row['id']; ?></td>
                                    <td><?php echo $row['patient_id']; ?></td>
                                    <td><?php echo $row['doctor_id']; ?></td>
                                    <td><?php echo $row['appointment_time']; ?></td>
                                    <td><?php echo $row['status']; ?></td>
                                    <td><a href="?view=appointments&edit=1&id=<?php echo $row['id']; ?>">Edit</a> <a href="?delete=1&type=appointment&id=<?php echo $row['id']; ?>" style="color:red; margin-left:10px;" onclick="return confirm('Delete?');">Delete</a></td>
                                </tr><?php endwhile; ?></tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>

</html>
<?php
$page_title = "Dashboard";
include 'header.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['user_id'];
$doctor_name = $_SESSION['username'];
$sql = "SELECT * FROM appointments WHERE doctor_id = '$doctor_id' ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<style>
    .dash-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .dash-header {
        margin-bottom: 30px;
    }

    .dash-header h1 {
        font-size: 28px;
        color: var(--text-dark);
    }

    .dash-header h1 span {
        color: var(--primary-color);
    }

    .appt-table {
        width: 100%;
        border-collapse: collapse;
        background: var(--white);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: var(--shadow-soft);
    }

    .appt-table th {
        background: var(--primary-color);
        color: white;
        padding: 15px;
        text-align: left;
        font-weight: 500;
    }

    .appt-table td {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
        color: var(--text-dark);
    }
</style>

<div class="dash-container">
    <div class="dash-header">
        <h1>Dr. <span><?php echo htmlspecialchars($doctor_name); ?></span>'s Dashboard</h1>
        <p style="color: var(--text-light);">Manage your upcoming appointments</p>
    </div>

    <table class="appt-table">
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Date & Time</th>
                <th>Reason</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['patient_name']); ?></strong></td>
                        <td><?php echo date('M d, Y h:i A', strtotime($row['appointment_time'])); ?></td>
                        <td><?php echo htmlspecialchars($row['reason']); ?></td>
                        <td><?php echo $row['status']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 30px; color: var(--text-light);">No appointments scheduled yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right;">
        <a href="logout.php" style="color: #ef4444; font-weight: 600; text-decoration: none;">Logout <i class="fas fa-sign-out-alt"></i></a>
    </div>
</div>

<?php include 'footer.php'; ?>
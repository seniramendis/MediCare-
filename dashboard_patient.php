<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit();
}
include 'header.php';

$patient_id = $_SESSION['user_id'];
$patient_name = $_SESSION['username'];

// --- STATS LOGIC ---
// 1. Total Due (Sum of UNPAID invoices)
$due_query = mysqli_query($conn, "SELECT SUM(amount) as total FROM invoices WHERE patient_id = '$patient_id' AND status = 'unpaid'");
$total_due = mysqli_fetch_assoc($due_query)['total'];

// 2. Counts
$my_appts_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM appointments WHERE patient_id = '$patient_id'"))['total'];
$my_rx_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM prescriptions WHERE patient_id = '$patient_id'"))['total'];


// FETCH DATA
$res_appts = mysqli_query($conn, "SELECT a.*, d.name as doctor_name FROM appointments a JOIN doctors d ON a.doctor_id = d.id WHERE a.patient_id = '$patient_id' ORDER BY a.appointment_time DESC");
$res_rx = mysqli_query($conn, "SELECT p.*, d.name as doctor_name FROM prescriptions p JOIN doctors d ON p.doctor_id = d.id WHERE p.patient_id = '$patient_id' ORDER BY p.created_at DESC");
// Fetch ONLY unpaid or show all? Let's show all but sort by unpaid first.
$res_inv = mysqli_query($conn, "SELECT i.*, d.name as doctor_name FROM invoices i JOIN doctors d ON i.doctor_id = d.id WHERE i.patient_id = '$patient_id' ORDER BY i.status DESC, i.created_at DESC");
?>

<style>
    /* Patient Dashboard Theme */
    :root {
        --dash-bg: #f8f9fa;
        --primary-color: #0c5adb;
        --text-dark: #1f2937;
        --text-light: #6b7280;
    }

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
        background: linear-gradient(135deg, var(--primary-color), #0946a8);
        color: white;
        padding: 40px;
        border-radius: 20px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(12, 90, 219, 0.2);
    }

    .welcome-banner h1 {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    /* STATS CARDS (NEW) */
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
        transition: 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
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
        background: #f3e8ff;
        color: #9333ea;
    }

    .icon-red {
        background: #fee2e2;
        color: #dc2626;
    }

    /* RED for DUE payments */
    .stat-info h3 {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
    }

    .content-box {
        background: white;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }

    .section-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 20px;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Rx & Tables */
    .rx-card {
        background: #eef2ff;
        border-left: 5px solid var(--primary-color);
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 8px;
    }

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    th {
        text-align: left;
        padding: 15px;
        color: var(--text-light);
        font-size: 14px;
        font-weight: 500;
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

    .b-pending {
        background: #fff7ed;
        color: #c2410c;
    }

    .b-confirmed {
        background: #e0f2fe;
        color: #0369a1;
    }

    .b-completed {
        background: #dcfce7;
        color: #15803d;
    }

    .b-unpaid {
        background: #fee2e2;
        color: #b91c1c;
    }

    .b-paid {
        background: #dcfce7;
        color: #15803d;
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

<div class="dash-container">
    <div class="welcome-banner">
        <h1>Hello, <?php echo htmlspecialchars($patient_name); ?>!</h1>
        <p>Welcome to your health dashboard.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon icon-blue"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-info">
                <h3><?php echo $my_appts_count; ?></h3>
                <p>Appointments</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-purple"><i class="fas fa-prescription"></i></div>
            <div class="stat-info">
                <h3><?php echo $my_rx_count; ?></h3>
                <p>Prescriptions</p>
            </div>
        </div>
        <div class="stat-card" style="border-left: 4px solid #dc2626;">
            <div class="stat-icon icon-red"><i class="fas fa-file-invoice-dollar"></i></div>
            <div class="stat-info">
                <h3>LKR <?php echo number_format($total_due ?? 0, 2); ?></h3>
                <p style="color: #dc2626; font-weight:600;">Total Due</p>
            </div>
        </div>
    </div>

    <div class="content-box">
        <div class="section-title"><i class="fas fa-calendar-alt"></i> My Appointments</div>
        <?php if (mysqli_num_rows($res_appts) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($res_appts)): $cls = ($row['status'] == 'confirmed') ? 'b-confirmed' : (($row['status'] == 'completed') ? 'b-completed' : 'b-pending'); ?>
                        <tr>
                            <td style="font-weight:600;">Dr. <?php echo htmlspecialchars($row['doctor_name']); ?></td>
                            <td><?php echo date('M d, Y - h:i A', strtotime($row['appointment_time'])); ?></td>
                            <td><span class="badge <?php echo $cls; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color:#999;">No appointments found.</p>
        <?php endif; ?>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <div class="content-box">
            <div class="section-title" style="color: var(--primary-color);"><i class="fas fa-prescription"></i> My Prescriptions</div>
            <?php if (mysqli_num_rows($res_rx) > 0): while ($rx = mysqli_fetch_assoc($res_rx)): ?>
                    <div class="rx-card">
                        <div class="rx-header"><span>Dr. <?php echo htmlspecialchars($rx['doctor_name']); ?></span> <span style="font-weight:400; font-size:12px;"><?php echo date('M d', strtotime($rx['created_at'])); ?></span></div>
                        <div style="font-size:13px; margin-bottom:5px; color:#555;">Dx: <?php echo htmlspecialchars($rx['diagnosis']); ?></div>
                        <div style="font-family:monospace;"><?php echo htmlspecialchars($rx['medication']); ?></div>
                    </div>
                <?php endwhile;
            else: ?><p style="color:#999;">No prescriptions.</p><?php endif; ?>
        </div>

        <div class="content-box">
            <div class="section-title" style="color: #16a34a;"><i class="fas fa-file-invoice-dollar"></i> My Invoices</div>
            <?php if (mysqli_num_rows($res_inv) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($inv = mysqli_fetch_assoc($res_inv)):
                            $is_paid = ($inv['status'] === 'paid');
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($inv['service_description']); ?></td>
                                <td style="font-weight:700; color:#16a34a;">LKR <?php echo number_format($inv['amount'], 2); ?></td>
                                <td>
                                    <?php if (!$is_paid): ?>
                                        <button class="btn-pay" data-id="<?php echo $inv['id']; ?>" style="background:#16a34a; color:white; border:none; padding:5px 15px; border-radius:5px; cursor:pointer;">Pay Now</button>
                                    <?php else: ?>
                                        <span class="badge b-paid">Paid</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color:#999;">No invoices found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.btn-pay').forEach(btn => {
        btn.addEventListener('click', async () => {
            if (!confirm("Pay this bill now?")) return;
            const id = btn.dataset.id;
            try {
                const res = await fetch('pay_bill.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `invoice_id=${id}`
                });
                const data = await res.json();
                if (data.success) {
                    alert("Payment Successful!");
                    location.reload(); // Reload to update "Total Due" stats
                } else {
                    alert("Error: " + data.message);
                }
            } catch (e) {
                console.error(e);
            }
        });
    });
</script>

<?php include 'footer.php'; ?>
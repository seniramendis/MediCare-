<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) exit();

$patient_id = $_SESSION['user_id'];
$q = mysqli_query($conn, "SELECT SUM(amount) as total FROM invoices WHERE patient_id='$patient_id' AND status='unpaid'");
$total = mysqli_fetch_assoc($q)['total'] ?? 0;
$list_q = mysqli_query($conn, "SELECT * FROM invoices WHERE patient_id='$patient_id' AND status='unpaid' ORDER BY created_at DESC");

echo '<h4 style="margin:0; color:#555;">Total Due</h4>';
echo '<div style="font-size: 32px; font-weight: 700; color: #dc3545; margin: 10px 0;">LKR ' . number_format($total, 2) . '</div>';

if (mysqli_num_rows($list_q) > 0) {
    echo '<table style="width:100%; border-collapse:collapse; font-size:13px;">';
    while ($bill = mysqli_fetch_assoc($list_q)) {
        // --- FIX: Read from the correct column 'service_description' ---
        $svc_name = $bill['service_description'];

        echo '<tr><td style="padding:10px 0; border-bottom:1px solid #eee;">' . htmlspecialchars($svc_name) . '</td>';
        echo '<td style="padding:10px 0; border-bottom:1px solid #eee; font-weight:bold;">' . number_format($bill['amount'], 2) . '</td>';
        echo '<td style="padding:10px 0; border-bottom:1px solid #eee; text-align:right;">';
        echo '<form action="payment_gateway.php" method="POST" style="margin:0;">';
        echo '<input type="hidden" name="invoice_id" value="' . $bill['id'] . '">';
        echo '<input type="hidden" name="amount" value="' . $bill['amount'] . '">';

        // --- FIX: Send it as 'service_name' so payment_gateway.php understands it ---
        echo '<input type="hidden" name="service_name" value="' . htmlspecialchars($svc_name) . '">';

        echo '<button type="submit" name="pay_now" style="background:#dc3545; color:white; border:none; padding:5px 10px; border-radius:4px; cursor:pointer;">Pay Now</button>';
        echo '</form></td></tr>';
    }
    echo '</table>';
} else {
    echo '<p style="font-size:13px; color:#28a745; margin-top:10px;">No pending bills.</p>';
}

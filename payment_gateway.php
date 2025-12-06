<?php
session_start();
include 'db_connect.php';

// 1. RECEIVE DATA (From Patient Dashboard)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$invoice_id = $_POST['invoice_id'] ?? '';
$amount = $_POST['amount'] ?? '';
$service_name = $_POST['service_name'] ?? 'Medical Service';

// 2. PROCESS PAYMENT (When User Clicks "Pay LKR...")
if (isset($_POST['confirm_payment'])) {
    $inv_id = $_POST['invoice_id'];
    $amt = $_POST['amount'];
    $patient_id = $_SESSION['user_id'];

    // Get Doctor ID from the invoice to credit them
    $doc_query = mysqli_query($conn, "SELECT doctor_id FROM invoices WHERE id='$inv_id'");
    $doc_data = mysqli_fetch_assoc($doc_query);
    $doctor_id = $doc_data['doctor_id'];

    // A. Update Invoice Status
    $update = "UPDATE invoices SET status='paid', payment_method='Secure Card' WHERE id='$inv_id'";
    mysqli_query($conn, $update);

    // B. Add to Payments Table (For Doctor's Revenue)
    $insert = "INSERT INTO payments (patient_id, doctor_id, amount, payment_method, paid_at) 
               VALUES ('$patient_id', '$doctor_id', '$amt', 'Secure Card', NOW())";

    if (mysqli_query($conn, $insert)) {
        echo "<script>alert('✅ Payment Successful!'); window.location.href='dashboard_patient.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Secure Checkout | Medicare Plus</title>
    <link rel="icon" href="images/Favicon.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/9e166a3863.js" crossorigin="anonymous"></script>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f3f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .checkout-wrapper {
            display: flex;
            background: white;
            width: 100%;
            max-width: 900px;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .payment-section {
            flex: 1.5;
            padding: 50px;
        }

        .summary-section {
            flex: 1;
            background: #f8faff;
            padding: 50px;
            border-left: 1px solid #eef2f6;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        h2 {
            font-size: 24px;
            color: #1a1f36;
            margin-bottom: 30px;
            font-weight: 700;
        }

        h3 {
            font-size: 14px;
            text-transform: uppercase;
            color: #8792a2;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #3c4257;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #aab7c4;
            font-size: 16px;
        }

        input {
            width: 100%;
            padding: 14px 16px 14px 45px;
            border: 1px solid #e6e8eb;
            border-radius: 8px;
            font-size: 16px;
            color: #3c4257;
            transition: all 0.2s ease;
            background: #fff;
        }

        input:focus {
            border-color: #0062cc;
            box-shadow: 0 0 0 4px rgba(0, 98, 204, 0.1);
            outline: none;
        }

        .row {
            display: flex;
            gap: 20px;
        }

        .col {
            flex: 1;
        }

        .btn-pay {
            width: 100%;
            background: #0062cc;
            color: white;
            padding: 16px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-pay:hover {
            background: #0056b3;
        }

        .cancel-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #697386;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .receipt-card {
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid #eef2f6;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 14px;
            color: #4f566b;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px dashed #e6e8eb;
            font-size: 22px;
            font-weight: 700;
            color: #1a1f36;
        }

        .secure-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 30px;
            color: #008000;
            font-size: 13px;
            font-weight: 500;
            background: #e6fffa;
            padding: 10px;
            border-radius: 6px;
        }

        @media (max-width: 768px) {
            .checkout-wrapper {
                flex-direction: column-reverse;
            }
        }
    </style>
</head>

<body>
    <div class="checkout-wrapper">
        <div class="payment-section">
            <h2>Payment Details</h2>
            <form method="POST">
                <input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>">
                <input type="hidden" name="amount" value="<?php echo $amount; ?>">

                <div class="form-group">
                    <label>Cardholder Name</label>
                    <div class="input-wrapper">
                        <i class="far fa-user input-icon"></i>
                        <input type="text" placeholder="John Doe" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Card Number</label>
                    <div class="input-wrapper">
                        <i class="far fa-credit-card input-icon"></i>
                        <input type="text" placeholder="0000 0000 0000 0000" maxlength="19" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col form-group">
                        <label>Expiration</label>
                        <div class="input-wrapper">
                            <i class="far fa-calendar-alt input-icon"></i>
                            <input type="text" placeholder="MM / YY" maxlength="5" required>
                        </div>
                    </div>
                    <div class="col form-group">
                        <label>CVC</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" placeholder="123" maxlength="4" required>
                        </div>
                    </div>
                </div>

                <button type="submit" name="confirm_payment" class="btn-pay">
                    <i class="fas fa-lock"></i> Pay LKR <?php echo number_format((float)$amount, 2); ?>
                </button>

                <a href="dashboard_patient.php" class="cancel-link">Cancel and Return</a>
            </form>
        </div>

        <div class="summary-section">
            <h3>Order Summary</h3>
            <div class="receipt-card">
                <div style="text-align:center; margin-bottom:20px;">
                    <div style="width:50px; height:50px; background:#e3f2fd; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; color:#0062cc; font-size:20px;">
                        <i class="fas fa-file-medical-alt"></i>
                    </div>
                    <h4 style="margin-top:10px; color:#333;">Medical Invoice</h4>
                </div>
                <div class="item-row">
                    <span>Invoice ID</span>
                    <span style="font-weight:600;">#<?php echo str_pad($invoice_id, 6, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="item-row">
                    <span>Service</span>
                    <span style="font-weight:600; color:#333;"><?php echo htmlspecialchars($service_name); ?></span>
                </div>
                <div class="item-row">
                    <span>Date</span>
                    <span><?php echo date('M d, Y'); ?></span>
                </div>
                <div class="total-row">
                    <span>Total</span>
                    <span style="color:#0062cc;">LKR <?php echo number_format((float)$amount, 2); ?></span>
                </div>
            </div>
            <div class="secure-badge">
                <i class="fas fa-shield-alt"></i> Payments are secure and encrypted
            </div>
        </div>
    </div>
</body>

</html>
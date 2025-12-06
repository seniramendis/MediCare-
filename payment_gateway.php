<?php
session_start();
include 'db_connect.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$invoice_id = $_POST['invoice_id'] ?? '';
$amount = $_POST['amount'] ?? '';
$service_name = $_POST['service_name'] ?? 'Medical Service';


if (isset($_POST['confirm_payment'])) {
    $inv_id = $_POST['invoice_id'];
    $amt = $_POST['amount'];
    $patient_id = $_SESSION['user_id'];


    $doc_query = mysqli_query($conn, "SELECT doctor_id FROM invoices WHERE id='$inv_id'");
    $doc_data = mysqli_fetch_assoc($doc_query);
    $doctor_id = $doc_data['doctor_id'];


    $update = "UPDATE invoices SET status='paid', payment_method='Secure Card' WHERE id='$inv_id'";
    mysqli_query($conn, $update);


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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout | Medicare Plus</title>
    <link rel="icon" href="images/Favicon.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/9e166a3863.js" crossorigin="anonymous"></script>

    <style>
        :root {
            --primary: #0c5adb;
            --primary-dark: #073d96;
            --accent: #f8f9fa;
            --text-dark: #1e293b;
            --text-light: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #eef2f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }


        .payment-container {
            display: flex;
            width: 100%;
            max-width: 950px;
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }


        .summary-panel {
            flex: 1;
            background: linear-gradient(135deg, #0c5adb 0%, #06b6d4 100%);
            color: white;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }


        .summary-panel::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .summary-panel::after {
            content: '';
            position: absolute;
            bottom: -50px;
            left: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .company-logo {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 1px;
            z-index: 2;
        }

        .order-details {
            z-index: 2;
            margin-top: 40px;
        }

        .detail-item {
            margin-bottom: 25px;
        }

        .detail-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 18px;
            font-weight: 600;
        }

        .total-amount {
            font-size: 42px;
            font-weight: 700;
            margin-top: 5px;
        }

        .footer-note {
            font-size: 12px;
            opacity: 0.7;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 5px;
        }


        .form-panel {
            flex: 1.4;
            padding: 50px;
            background: #ffffff;
        }

        .form-header {
            margin-bottom: 30px;
        }

        .form-header h2 {
            font-size: 24px;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .card-icons {
            display: flex;
            gap: 10px;
            color: var(--text-light);
            font-size: 24px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .input-box {
            position: relative;
        }

        .input-box input {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            transition: 0.3s;
            background: #f8fafc;
        }

        .input-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .input-box input:focus {
            outline: none;
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(12, 90, 219, 0.1);
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
            padding: 16px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 20px;
            box-shadow: 0 10px 20px rgba(12, 90, 219, 0.2);
        }

        .btn-pay:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .cancel-btn {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--text-light);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: 0.3s;
        }

        .cancel-btn:hover {
            color: var(--text-dark);
        }

        @media (max-width: 850px) {
            .payment-container {
                flex-direction: column;
            }

            .summary-panel {
                padding: 30px;
                border-radius: 20px 20px 0 0;
            }

            .form-panel {
                padding: 30px;
            }
        }
    </style>
</head>

<body>

    <div class="payment-container">

        <div class="summary-panel">
            <div class="company-logo">
                <i class="fas fa-heartbeat"></i> MediCare+
            </div>

            <div class="order-details">
                <div class="detail-item">
                    <div class="detail-label">Service Description</div>
                    <div class="detail-value"><?php echo htmlspecialchars($service_name); ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Invoice Number</div>
                    <div class="detail-value">#<?php echo str_pad($invoice_id, 6, '0', STR_PAD_LEFT); ?></div>
                </div>

                <div class="detail-item" style="margin-top: 40px;">
                    <div class="detail-label">Total to Pay</div>
                    <div class="total-amount">LKR <?php echo number_format((float)$amount, 2); ?></div>
                </div>
            </div>

            <div class="footer-note">
                <i class="fas fa-lock"></i> Secure 256-bit SSL Encrypted payment
            </div>
        </div>

        <div class="form-panel">
            <div class="form-header">
                <h2>Payment Details</h2>
                <div class="card-icons">
                    <i class="fab fa-cc-visa" style="color: #1a1f71;"></i>
                    <i class="fab fa-cc-mastercard" style="color: #eb001b;"></i>
                    <i class="fab fa-cc-amex" style="color: #2e77bc;"></i>
                </div>
            </div>

            <form method="POST">
                <input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>">
                <input type="hidden" name="amount" value="<?php echo $amount; ?>">

                <div class="input-group">
                    <label>Name on Card</label>
                    <div class="input-box">
                        <i class="far fa-user"></i>
                        <input type="text" placeholder="e.g. Jehan Fernando" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>Card Number</label>
                    <div class="input-box">
                        <i class="far fa-credit-card"></i>
                        <input type="text" placeholder="0000 0000 0000 0000" maxlength="19" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col input-group">
                        <label>Expiry Date</label>
                        <div class="input-box">
                            <i class="far fa-calendar-alt"></i>
                            <input type="text" placeholder="MM / YY" maxlength="5" required>
                        </div>
                    </div>
                    <div class="col input-group">
                        <label>CVV / CVC</label>
                        <div class="input-box">
                            <i class="fas fa-lock"></i>
                            <input type="password" placeholder="123" maxlength="4" required>
                        </div>
                    </div>
                </div>

                <button type="submit" name="confirm_payment" class="btn-pay">
                    Confirm Payment
                </button>

                <a href="dashboard_patient.php" class="cancel-btn">Cancel Transaction</a>
            </form>
        </div>

    </div>

</body>

</html>
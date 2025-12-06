<?php
session_start();
include 'db_connect.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? '';
$row = [];


if ($id) {
    $sql = "SELECT * FROM invoices WHERE id='$id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
}


if (isset($_POST['update_btn'])) {
    $id = $_POST['id'];
    $service = $_POST['service'];
    $amount = $_POST['amount'];

    $sql = "UPDATE invoices SET service_name='$service', amount='$amount', is_edited=1 WHERE id='$id'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('✅ Invoice updated!'); window.location.href='dashboard_doctor.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Invoice</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f3f6f8;
            padding: 40px;
            display: flex;
            justify-content: center;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            width: 400px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: #0062cc;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="card">
        <h2>Edit Invoice #<?= $id ?></h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <label>Service Name</label>
            <input type="text" name="service" value="<?= $row['service_name'] ?>" required>
            <label>Amount (LKR)</label>
            <input type="number" name="amount" value="<?= $row['amount'] ?>" required>
            <button type="submit" name="update_btn" class="btn">Update Invoice</button>
            <br><br>
            <a href="dashboard_doctor.php" style="display:block; text-align:center; text-decoration:none; color:#666;">Cancel</a>
        </form>
    </div>
</body>

</html>
<?php
$page_title = "Admin Portal";
session_start();
include 'db_connect.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            if ($row['role'] === 'admin') {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['full_name'];
                $_SESSION['role'] = $row['role'];

                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error = "Access Denied. You do not have admin privileges.";
            }
        } else {
            $error = "Invalid Credentials";
        }
    } else {
        $error = "Invalid Credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | MediCare+</title>
    <link rel="icon" href="images/favicon.png">
    <script src="https://kit.fontawesome.com/9e166a3863.js" crossorigin="anonymous"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            /* Deep Dark Slate for Professional look */
            background-color: #0f172a;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .admin-login-box {
            background: #1e293b;
            /* Lighter slate for the card */
            width: 400px;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            text-align: center;
            border: 1px solid #334155;
            color: white;
        }

        .lock-icon {
            font-size: 40px;
            color: #3b82f6;
            /* Changed from Red to Blue */
            margin-bottom: 20px;
        }

        h2 {
            margin-bottom: 5px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        p.subtitle {
            color: #94a3b8;
            font-size: 13px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            color: #cbd5e1;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            background: #334155;
            border: 1px solid #475569;
            border-radius: 6px;
            color: white;
            font-size: 14px;
            outline: none;
            transition: 0.3s;
        }

        input:focus {
            border-color: #3b82f6;
            /* Blue Focus */
            background: #475569;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: #2563eb;
            /* Royal Blue Button */
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #1d4ed8;
            /* Darker Blue on Hover */
        }

        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            padding: 10px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .back-link {
            display: block;
            margin-top: 25px;
            font-size: 13px;
            color: #64748b;
            text-decoration: none;
            transition: 0.3s;
        }

        .back-link:hover {
            color: #94a3b8;
        }
    </style>
</head>

<body>

    <div class="admin-login-box">
        <i class="fas fa-shield-alt lock-icon"></i>
        <h2>Admin Portal</h2>
        <p class="subtitle">Secure System Access</p>

        <?php if ($error): ?>
            <div class="error-msg">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Administrator Email</label>
                <input type="email" name="email" required autocomplete="off">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn-login">Login to Dashboard</button>
        </form>

        <a href="index.php" class="back-link">← Return to Website</a>
    </div>

</body>

</html>
<?php
session_start();
include 'db_connect.php';

$error = "";
$email_input = ""; // To keep email in box if error occurs

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $email_input = $email;

    // --- 1. SEARCH IN USERS TABLE (PATIENTS & ADMINS) ---
    $sql_user = "SELECT * FROM users WHERE email='$email'";
    $result_user = mysqli_query($conn, $sql_user);

    // --- 2. SEARCH IN DOCTORS TABLE ---
    // (This handles the "Doctor Login" you asked about - it checks automatically)
    $sql_doc = "SELECT * FROM doctors WHERE email='$email'";
    $result_doc = mysqli_query($conn, $sql_doc);

    // LOGIC: CHECK WHERE THE USER WAS FOUND
    if (mysqli_num_rows($result_user) > 0) {
        // Found in Users table (Patient or Admin)
        $row = mysqli_fetch_assoc($result_user);

        // --- SECURITY: BLOCK ADMINS ---
        if ($row['role'] === 'admin') {
            $error = "Admins cannot login here.<br>Please use the <a href='admin_dashboard.php'>Admin Panel</a>.";
        }
        // --- ALLOW PATIENTS ---
        elseif (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['full_name'];
            $_SESSION['role'] = 'patient';

            header("Location: dashboard_patient.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } elseif ($result_doc && mysqli_num_rows($result_doc) > 0) {
        // --- FOUND IN DOCTORS TABLE ---
        $row = mysqli_fetch_assoc($result_doc);

        // Note: Use password_verify if you hashed doctor passwords. 
        // If doctors have plain text passwords in DB, use: if ($password == $row['password'])
        // Assuming hashed for security:
        if ($password == $row['password'] || password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['name'];
            $_SESSION['role'] = 'doctor';

            header("Location: dashboard_doctor.php");
            exit();
        } else {
            $error = "Incorrect password for Doctor account.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login | MediCare+</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect width=%22100%22 height=%22100%22 rx=%2220%22 fill=%22%230c5adb%22/><path fill=%22%23ffffff%22 d=%22M35 20h30v25h25v30h-25v25h-30v-25h-25v-30h25z%22/></svg>">

    <script src="https://kit.fontawesome.com/9e166a3863.js" crossorigin="anonymous"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-color: #0c5adb;
            --primary-dark: #0946a8;
            --accent-color: #ff4d6d;
            --text-dark: #1f2937;
            --text-light: #6b7280;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            /* Restoring your background image style */
            background: linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.9)), url('images/background3.png');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-card {
            background: white;
            padding: 50px 40px;
            width: 100%;
            max-width: 450px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(12, 90, 219, 0.15);
            text-align: center;
        }

        .logo {
            font-size: 32px;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 10px;
            display: inline-block;
            text-decoration: none;
        }

        .logo span {
            color: var(--accent-color);
        }

        h2 {
            color: var(--text-dark);
            margin-bottom: 30px;
            font-weight: 600;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-dark);
            font-weight: 500;
            font-size: 14px;
        }

        .input-box {
            position: relative;
        }

        .input-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            /* space for icon */
            border: 1px solid #e5e7eb;
            border-radius: 50px;
            font-size: 14px;
            outline: none;
            transition: 0.3s;
            background: #f9fafb;
        }

        input:focus {
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 4px rgba(12, 90, 219, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(12, 90, 219, 0.3);
        }

        .btn-login:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .error-msg {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.5;
        }

        .error-msg a {
            color: #b91c1c;
            text-decoration: underline;
            font-weight: bold;
        }

        .footer-links {
            margin-top: 25px;
            font-size: 14px;
            color: var(--text-light);
        }

        .footer-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        .divider {
            margin: 20px 0;
            border-top: 1px solid #e5e7eb;
            position: relative;
        }

        .divider span {
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 0 10px;
            color: #9ca3af;
            font-size: 12px;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <a href="Home.php" class="logo"><span>M</span>edi<span>C</span>are+</a>
        <h2>Welcome Back</h2>

        <?php if ($error): ?>
            <div class="error-msg">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <div class="input-box">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email_input); ?>" placeholder="Enter your email" required>
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-box">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
            </div>

            <button type="submit" name="login" class="btn-login">Login</button>
        </form>

        <div class="divider"><span>OR</span></div>

        <div class="footer-links">
            Don't have an account? <a href="register.php">Register Patient</a> <br><br>
            <a href="Home.php" style="color: var(--text-light); font-weight: normal;"><i class="fas fa-arrow-left"></i> Back to Home</a>
        </div>
    </div>

</body>

</html>
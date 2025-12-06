<?php
session_start();
include 'db_connect.php';

$error = "";
$email_input = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $login_type = $_POST['login_type'];
    $email_input = $email;

    // ==========================================
    // OPTION 1: PATIENT LOGIN
    // ==========================================
    if ($login_type === 'patient') {
        // Search in the 'users' table
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            // Verify Password
            $check_password = ($password === $row['password']) || password_verify($password, $row['password']);

            if ($check_password) {
                // Verify correct role (Prevent doctors from logging in as patients if they are in this table too)
                if (isset($row['role']) && $row['role'] !== 'patient') {
                    $error = "Access Denied. This email belongs to a Doctor.";
                } else {
                    // Login Success
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['full_name']; // Assuming 'users' has full_name
                    $_SESSION['role'] = 'patient';
                    header("Location: dashboard_patient.php");
                    exit();
                }
            } else {
                $error = "Correct Email, but <b>Wrong Password</b>.";
            }
        } else {
            $error = "No Patient account found with this email.";
        }
    }

    // ==========================================
    // OPTION 2: DOCTOR LOGIN
    // ==========================================
    elseif ($login_type === 'doctor') {
        // Search in the 'doctors' table (Matches your Screenshot)
        $sql = "SELECT * FROM doctors WHERE email='$email'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            // Verify Password
            $check_password = ($password === $row['password']) || password_verify($password, $row['password']);

            if ($check_password) {
                // Login Success
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['name']; // 'doctors' table uses 'name'
                $_SESSION['role'] = 'doctor';
                header("Location: dashboard_doctor.php");
                exit();
            } else {
                $error = "Correct Email, but <b>Wrong Password</b>.";
            }
        } else {
            $error = "No Doctor account found with this email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login | MediCare+</title>
    <link rel="icon" href="images/Favicon.png" type="image/png">
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
            transition: 0.3s ease;
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
            transition: 0.3s;
        }

        .header-doctor {
            color: #059669;
        }

        .btn-doctor {
            background-color: #059669 !important;
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
            border: 1px solid #fecaca;
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

        .doctor-toggle {
            cursor: pointer;
            color: var(--primary-color);
            font-weight: 600;
            margin-top: 15px;
            display: inline-block;
        }
    </style>
</head>

<body>

    <div class="login-card" id="loginCard">
        <a href="Home.php" class="logo"><span>M</span>edi<span>C</span>are+</a>
        <h2 id="loginTitle">Patient Login</h2>

        <?php if ($error): ?>
            <div class="error-msg"><i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="login_type" id="loginType" value="patient">

            <div class="form-group">
                <label>Email Address</label>
                <div class="input-box">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email_input); ?>" placeholder="Enter email" required>
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-box">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Enter password" required>
                </div>
            </div>
            <button type="submit" name="login" class="btn-login" id="loginBtn">Sign In</button>
        </form>

        <div class="footer-links">
            <span id="registerLinkText">Don't have an account? <a href="register.php">Register Patient</a></span>
            <br>

            <div class="divider"><span>OR</span></div>
            <p id="toggleText">Are you a doctor?</p>
            <a class="doctor-toggle" onclick="toggleLoginMode()">Login here as Doctor</a>

            <br><br>
            <a href="Home.php" style="color: var(--text-light); font-weight: normal;"><i class="fas fa-arrow-left"></i> Back to Home</a>
        </div>
    </div>

    <script>
        function toggleLoginMode() {
            const title = document.getElementById('loginTitle');
            const btn = document.getElementById('loginBtn');
            const typeInput = document.getElementById('loginType');
            const toggleText = document.getElementById('toggleText');
            const toggleLink = document.querySelector('.doctor-toggle');
            const registerText = document.getElementById('registerLinkText');

            if (typeInput.value === 'patient') {
                // Switch to Doctor Mode
                typeInput.value = 'doctor';
                title.innerText = 'Doctor Login';
                title.classList.add('header-doctor');
                btn.classList.add('btn-doctor');

                toggleText.innerText = 'Are you a patient?';
                toggleLink.innerText = 'Login here as Patient';
                registerText.style.display = 'none';
            } else {
                // Switch back to Patient Mode
                typeInput.value = 'patient';
                title.innerText = 'Patient Login';
                title.classList.remove('header-doctor');
                btn.classList.remove('btn-doctor');

                toggleText.innerText = 'Are you a doctor?';
                toggleLink.innerText = 'Login here as Doctor';
                registerText.style.display = 'inline';
            }
        }
    </script>

</body>

</html>
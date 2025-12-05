<?php
session_start();
include 'db_connect.php';

$error = "";
$email_input = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // Do not escape password before verifying
    $login_type = $_POST['login_type'];
    $email_input = $email;

    if ($login_type === 'patient') {
        // --- PATIENT LOGIN ---
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            // Check: Plain Text OR Hash
            if ($password == $row['password'] || password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['full_name'];
                $_SESSION['role'] = 'patient';
                header("Location: dashboard_patient.php");
                exit();
            } else {
                $error = "Correct Email, but <b>Wrong Password</b>.";
            }
        } else {
            // Check if they are actually a doctor trying to login as patient
            $check_doc = mysqli_query($conn, "SELECT * FROM doctors WHERE email='$email'");
            if (mysqli_num_rows($check_doc) > 0) {
                $error = "This email belongs to a Doctor! <br><b>Please click 'Login here as Doctor' below.</b>";
            } else {
                $error = "No Patient account found with this email.";
            }
        }
    } elseif ($login_type === 'doctor') {
        // --- DOCTOR LOGIN ---
        $sql = "SELECT * FROM doctors WHERE email='$email'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            // Check: Plain Text OR Hash (Fixes the kavinga123 issue)
            if ($password == $row['password'] || password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['name'];
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
            background: linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.9)), url('images/background3.png');
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
            background: #f9fafb;
        }

        input:focus {
            border-color: var(--primary-color);
            background: white;
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
            margin-top: 10px;
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
                typeInput.value = 'doctor';
                title.innerText = 'Doctor Login';
                title.classList.add('header-doctor');
                btn.classList.add('btn-doctor');
                toggleText.innerText = 'Are you a patient?';
                toggleLink.innerText = 'Login here as Patient';
                registerText.style.display = 'none';
            } else {
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
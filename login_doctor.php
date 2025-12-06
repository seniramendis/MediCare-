<?php
$page_title = "Doctor Login";
session_start();
include 'db_connect.php';

// --- DOCTOR LOGIN LOGIC ---
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {

            // OPTIONAL: Enforce Role Check
            if ($row['role'] !== 'doctor') {
                $error = "Access Denied. This portal is for doctors only.";
            } else {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['full_name'];
                $_SESSION['role'] = $row['role'];

                header("Location: dashboard_doctor.php");
                exit();
            }
        } else {
            $error = "Invalid Password";
        }
    } else {
        $error = "Doctor account not found";
    }
}
// --- END LOGIC ---

include 'header.php';
?>

<style>
    .login-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        background-color: #f0f7ff;
        /* Slight blue tint for doctor page */
    }

    .login-box {
        background: var(--white);
        width: 400px;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 15px 50px rgba(12, 90, 219, 0.15);
        /* Blue shadow */
        text-align: center;
        border: 2px solid #e0f2fe;
    }

    .login-box h2 {
        color: #0c5adb;
        margin-bottom: 5px;
        font-weight: 800;
    }

    .login-box input {
        width: 100%;
        padding: 14px;
        margin: 10px 0;
        border: 1px solid #bce3ff;
        border-radius: 50px;
        background: #f0f9ff;
        text-align: center;
        outline: none;
        transition: 0.3s;
    }

    .login-box input:focus {
        border-color: #0c5adb;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(12, 90, 219, 0.1);
    }

    .login-box button {
        width: 100%;
        margin-top: 10px;
        padding: 14px;
        background-color: #0c5adb;
    }

    .error {
        color: #b91c1c;
        font-size: 14px;
        margin-bottom: 10px;
        background: #fee2e2;
        padding: 10px;
        border-radius: 8px;
    }

    .divider {
        margin: 20px 0;
        border-top: 1px solid #eee;
    }
</style>

<div class="login-wrapper">
    <div class="login-box">
        <i class="fas fa-user-md" style="font-size: 40px; color: #0c5adb; margin-bottom: 15px;"></i>
        <h2>Doctor Portal</h2>
        <p style="color:var(--text-light); margin-bottom:20px;">Please login to continue</p>

        <?php if ($error) echo "<p class='error'>$error</p>"; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Doctor Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn">Access Dashboard</button>
        </form>

        <div class="divider"></div>


        <p style="font-size: 14px; color: var(--text-light);">
            Are you a patient?
            <a href="login.php" style="color: var(--text-dark); font-weight: 600; text-decoration: none;">Login here</a>
        </p>
    </div>
</div>

<?php include 'footer.php'; ?>
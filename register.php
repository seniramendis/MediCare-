<?php
$page_title = "Register";
// Include header (handles Session, DB connection, and Global CSS)
include 'header.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Collect & Sanitize Input
    $full_name = mysqli_real_escape_string($conn, $_POST['name']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $phone     = mysqli_real_escape_string($conn, $_POST['phone']);
    $dob       = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender    = mysqli_real_escape_string($conn, $_POST['gender']);
    $address   = mysqli_real_escape_string($conn, $_POST['address']);

    $password  = $_POST['password'];
    $confirm_p = $_POST['confirm_password'];

    // Default Role
    $role      = 'patient';

    // 2. Validation Checks
    $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");

    if (mysqli_num_rows($check_email) > 0) {
        $error = "Email already registered!";
    } elseif ($password !== $confirm_p) {
        $error = "Passwords do not match!";
    } else {
        // 3. Secure Hash & Insert
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (full_name, email, phone, dob, gender, address, password, role) 
                VALUES ('$full_name', '$email', '$phone', '$dob', '$gender', '$address', '$hashed_password', '$role')";

        if (mysqli_query($conn, $sql)) {
            $success = "Account created successfully! Redirecting...";
            echo "<script>setTimeout(function(){ window.location.href='login.php'; }, 2000);</script>";
        } else {
            $error = "Database Error: " . mysqli_error($conn);
        }
    }
}
?>

<style>
    /* Page Specific Styles */
    .register-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 90vh;
        padding: 60px 20px;
    }

    .register-box {
        background: var(--white);
        width: 100%;
        max-width: 700px;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .register-box h2 {
        color: var(--primary-color);
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 5px;
        text-align: center;
    }

    .register-box p.subtitle {
        color: var(--text-light);
        margin-bottom: 30px;
        font-size: 15px;
        text-align: center;
    }

    /* Grid Layout for Form */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .full-width {
        grid-column: span 2;
    }

    .input-group {
        text-align: left;
    }

    .input-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
        font-weight: 500;
        color: var(--text-dark);
    }

    .register-box input,
    .register-box select,
    .register-box textarea {
        width: 100%;
        padding: 12px;
        font-size: 14px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #f9fafb;
        outline: none;
        transition: all 0.3s ease;
        font-family: inherit;
    }

    .register-box input:focus,
    .register-box select:focus,
    .register-box textarea:focus {
        border-color: var(--primary-color);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(12, 90, 219, 0.1);
    }

    .register-box button {
        width: 100%;
        margin-top: 25px;
        padding: 14px;
        font-size: 16px;
    }

    /* Alerts */
    .alert {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 500;
        text-align: center;
    }

    .alert-error {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .alert-success {
        background-color: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    @media (max-width: 600px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .full-width {
            grid-column: span 1;
        }
    }
</style>

<div class="register-wrapper">
    <div class="register-box">
        <h2>Patient Registration</h2>
        <p class="subtitle">Complete your profile to get started</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="" method="post">
            <div class="form-grid">

                <div class="input-group full-width">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="Jehan Fernando" required>
                </div>

                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="name@example.com" required>
                </div>

                <div class="input-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" placeholder="07X XXXXXXX" required>
                </div>

                <div class="input-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" required>
                </div>

                <div class="input-group">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="input-group full-width">
                    <label>Residential Address</label>
                    <input type="text" name="address" placeholder="Street, City, Postal Code" required>
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <div class="input-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required>
                </div>

            </div>

            <button type="submit" class="btn">Create Account</button>
        </form>

        <p style="margin-top: 25px; font-size: 14px; text-align: center; color: var(--text-light);">
            Already have an account?
            <a href="login.php" style="color: var(--primary-color); font-weight: 600; text-decoration: none;">Login</a>
        </p>
    </div>
</div>

<?php include 'footer.php'; ?>
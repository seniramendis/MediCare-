<?php
$page_title = "Edit Profile";

// 1. START SESSION SAFELY
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';

// 2. CHECK LOGIN
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$msg_type = "";

// 3. FETCH CURRENT USER DATA (To pre-fill the form)
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// 4. HANDLE FORM SUBMISSION
if (isset($_POST['update_profile'])) {
    // Sanitize Basic Inputs
    $full_name = mysqli_real_escape_string($conn, $_POST['name']); // input name='name'
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $phone     = mysqli_real_escape_string($conn, $_POST['phone']);
    $dob       = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender    = mysqli_real_escape_string($conn, $_POST['gender']);
    $address   = mysqli_real_escape_string($conn, $_POST['address']);

    // --- PASSWORD UPDATE LOGIC ---
    $password_update_sql = ""; // Default: empty (no password change)
    $allow_update = true;      // Flag to stop update if password checks fail

    // Check if user is trying to change password
    if (!empty($_POST['current_password'])) {
        $current_password = $_POST['current_password'];
        $new_password     = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // A. Verify Current Password matches DB
        // Note: Using password_verify for security (Matches your Register code)
        if (password_verify($current_password, $user['password'])) {

            // B. Check if New Password matches Confirm
            if ($new_password === $confirm_password && !empty($new_password)) {
                $hashed_new_pass = password_hash($new_password, PASSWORD_DEFAULT);
                $password_update_sql = ", password = '$hashed_new_pass'";
            } else {
                $message = "New passwords do not match or are empty!";
                $msg_type = "error";
                $allow_update = false;
            }
        } else {
            $message = "Current password is incorrect!";
            $msg_type = "error";
            $allow_update = false;
        }
    }

    // --- EXECUTE UPDATE ---
    if ($allow_update) {
        $update_sql = "UPDATE users SET 
                       full_name = '$full_name', 
                       email = '$email', 
                       phone = '$phone',
                       dob = '$dob',
                       gender = '$gender',
                       address = '$address'
                       $password_update_sql 
                       WHERE id = '$user_id'";

        if (mysqli_query($conn, $update_sql)) {
            // Update Session Name Immediately
            $_SESSION['username'] = $full_name;

            $message = "Profile updated successfully!";
            $msg_type = "success";

            // Refresh data to show updates
            $result = mysqli_query($conn, $sql);
            $user = mysqli_fetch_assoc($result);
        } else {
            $message = "Database Error: " . mysqli_error($conn);
            $msg_type = "error";
        }
    }
}

include 'header.php';
?>

<style>
    /* Reusing your Registration Style for Consistency */
    body {
        background-color: #f3f4f6;
    }

    .profile-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 85vh;
        padding: 40px 20px;
    }

    .profile-box {
        background: white;
        width: 100%;
        max-width: 800px;
        /* Wider for 2 columns */
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .profile-box h2 {
        color: #0c5adb;
        font-size: 26px;
        font-weight: 700;
        margin-bottom: 5px;
        text-align: center;
    }

    .profile-box p.subtitle {
        color: #6b7280;
        margin-bottom: 30px;
        font-size: 14px;
        text-align: center;
    }

    /* Grid Layout */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .full-width {
        grid-column: span 2;
    }

    .input-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
    }

    .input-group input,
    .input-group select {
        width: 100%;
        padding: 12px;
        font-size: 14px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #f9fafb;
        outline: none;
        transition: 0.3s;
    }

    .input-group input:focus,
    .input-group select:focus {
        border-color: #0c5adb;
        background: white;
        box-shadow: 0 0 0 4px rgba(12, 90, 219, 0.1);
    }

    /* Section Divider */
    .section-divider {
        grid-column: span 2;
        border-top: 1px solid #e5e7eb;
        margin: 20px 0 10px 0;
        padding-top: 10px;
        font-size: 16px;
        font-weight: 700;
        color: #1f2937;
    }

    .btn-update {
        grid-column: span 2;
        width: 100%;
        margin-top: 20px;
        padding: 14px;
        background: #0c5adb;
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
        box-shadow: 0 4px 15px rgba(12, 90, 219, 0.2);
    }

    .btn-update:hover {
        background: #0946a8;
        transform: translateY(-2px);
    }

    .alert {
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 25px;
        text-align: center;
        font-size: 14px;
        font-weight: 500;
    }

    .alert-success {
        background: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
    }

    .alert-error {
        background: #f8d7da;
        color: #842029;
        border: 1px solid #f5c6cb;
    }

    @media (max-width: 650px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .full-width {
            grid-column: span 1;
        }

        .section-divider {
            grid-column: span 1;
        }

        .btn-update {
            grid-column: span 1;
        }
    }
</style>

<div class="profile-wrapper">
    <div class="profile-box">
        <h2>Update Profile</h2>
        <p class="subtitle">Keep your personal details up to date</p>

        <?php if ($message): ?>
            <div class="alert <?php echo ($msg_type == 'success') ? 'alert-success' : 'alert-error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-grid">

                <!-- PERSONAL DETAILS SECTION -->
                <div class="input-group full-width">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required>
                </div>

                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                </div>

                <div class="input-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                </div>

                <div class="input-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" value="<?php echo htmlspecialchars($user['dob'] ?? ''); ?>" required>
                </div>

                <div class="input-group">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="Male" <?php if (($user['gender'] ?? '') == 'Male') echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if (($user['gender'] ?? '') == 'Female') echo 'selected'; ?>>Female</option>
                        <option value="Other" <?php if (($user['gender'] ?? '') == 'Other') echo 'selected'; ?>>Other</option>
                    </select>
                </div>

                <div class="input-group full-width">
                    <label>Residential Address</label>
                    <input type="text" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" required>
                </div>

                <!-- PASSWORD CHANGE SECTION -->
                <div class="section-divider">
                    Change Password <span style="font-weight: 400; font-size: 13px; color: #6b7280; margin-left: 10px;">(Leave blank to keep current)</span>
                </div>

                <div class="input-group full-width">
                    <label>Current Password <span style="color:red">*</span></label>
                    <input type="password" name="current_password" placeholder="Enter current password to make changes">
                </div>

                <div class="input-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" placeholder="New Password">
                </div>

                <div class="input-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" placeholder="Confirm New Password">
                </div>

                <button type="submit" name="update_profile" class="btn-update">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
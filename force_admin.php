<?php
include 'db_connect.php';

// 1. CLEANUP: Delete any existing admin with this email to avoid duplicates
mysqli_query($conn, "DELETE FROM users WHERE email='admin@medicare.com'");

// 2. CONFIG: Set the credentials
$email = "admin@medicare.com";
$pass  = "admin123";
$role  = "admin";
$name  = "System Admin";

// 3. HASH: Let YOUR server generate the hash (Guaranteed to match)
$hash = password_hash($pass, PASSWORD_DEFAULT);

// 4. INSERT
$sql = "INSERT INTO users (full_name, email, password, role) VALUES ('$name', '$email', '$hash', '$role')";

if (mysqli_query($conn, $sql)) {
    echo "<div style='font-family:sans-serif; text-align:center; margin-top:50px;'>";
    echo "<h1 style='color:green'>✅ Admin Account Reset Successfully</h1>";
    echo "<p>I have deleted the old account and created a fresh one.</p>";
    echo "<hr style='width:300px'>";
    echo "<p><strong>Email:</strong> admin@medicare.com</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<br>";
    echo "<a href='admin_login.php' style='background:red; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Try Login Now</a>";
    echo "</div>";
} else {
    echo "<h1>Database Error:</h1>";
    echo mysqli_error($conn);
}

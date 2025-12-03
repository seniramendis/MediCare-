<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = md5($_POST['password']);
    $role     = $_POST['role'];  // admin / doctor / patient

    // Check if email already exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($check) > 0) {
        echo "<script>
            alert('Email already registered!');
            window.location.href='register.html';
        </script>";
        exit();
    }

    // Insert user
    $sql = "INSERT INTO users (name, email, password, role)
            VALUES ('$name', '$email', '$password', '$role')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
            alert('Registered Successfully!');
            window.location.href='login.html';
        </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>


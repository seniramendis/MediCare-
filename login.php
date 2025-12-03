<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $role = $_POST['role'];
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM login 
            WHERE username='$username' 
            AND password='$password' 
            AND role='$role'";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {

        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;

        // Redirect to Home Page
        header("Location: Home.html");
        exit();
    } 
    else {
        echo "<script>
            alert('Invalid Username or Password!');
            window.location.href='login.html';
        </script>";
    }
}
?>


<?php
session_start();
include('db.php');

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$user' AND password = '$pass'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['username'] = $row['username'];
        $_SESSION['roleId'] = $row['roleId'];

        if ($row['roleId'] == 1) {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: partner_dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>
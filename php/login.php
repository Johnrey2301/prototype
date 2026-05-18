<?php
session_start();
include('connection.php');

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? $_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($email === '' || $pass === '') {
        $error = "Please enter email and password!";
    } else {
        // Match new schema: users(email,password,roleId)
        $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$pass'";

        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            // session used across the app; keep both keys for compatibility
            $_SESSION['username'] = $row['email'] ?? ($row['username'] ?? null);
            $_SESSION['roleId'] = $row['roleId'];


            if ((int)$row['roleId'] === 1) {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: partner_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid username or password!";
        }
    }
}
?>

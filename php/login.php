<?php
session_start();
include('connection.php');

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($user === '' || $pass === '') {
        $error = "Please enter username and password!";
    } else {
        // Match schema used by signup.php: users(username,password,roleId)
        $sql = "SELECT * FROM users WHERE username = '$user' AND password = '$pass'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['username'] = $row['username'];
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

<?php
session_start();
include('connection.php');

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstname   = trim($_POST['firstname'] ?? '');
    $lastname    = trim($_POST['lastname'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $phonenumber = trim($_POST['phonenumber'] ?? '');

    $pass  = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    if ($firstname === '' || $lastname === '' || $email === '' || $phonenumber === '') {
        $error = "Please fill out firstname, lastname, email, and phone number.";
    } else if ($pass === '' || $pass2 === '') {
        $error = "Please fill out password.";
    } else if ($pass !== $pass2) {
        $error = "Passwords do not match.";
    } else {
        $roleId = 2; // default broker

        // Check if email already exists (prepared statement)
        $checkStmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? LIMIT 1");
        if (!$checkStmt) {
            $error = "Server error (prepare failed).";
        } else {
            mysqli_stmt_bind_param($checkStmt, 's', $email);
            mysqli_stmt_execute($checkStmt);
            $checkRes = mysqli_stmt_get_result($checkStmt);

            if ($checkRes && mysqli_num_rows($checkRes) > 0) {
                $error = "Email already exists!";
            } else {
                // Insert user (prepared statement)
                $insertStmt = mysqli_prepare(
                    $conn,
                    "INSERT INTO users (firstname, lastname, email, phonenumber, password, roleId)
                     VALUES (?, ?, ?, ?, ?, ?)"
                );

                if (!$insertStmt) {
                    $error = "Server error (prepare failed).";
                } else {
                    mysqli_stmt_bind_param(
                        $insertStmt,
                        'sssssi',
                        $firstname,
                        $lastname,
                        $email,
                        $phonenumber,
                        $pass,
                        $roleId
                    );

                    $ok = mysqli_stmt_execute($insertStmt);

                    if ($ok) {
                        $_SESSION['email'] = $email;
                        $_SESSION['roleId'] = $roleId;

                        header("Location: ../dboard/login.html?signup=success");
                        exit();
                    }

                    $error = "Failed to create account.";
                }
            }
        }
    }
}

if ($error !== "") {
    echo "<script>alert(" . json_encode($error) . "); window.location.href = '../dboard/signup.html';</script>";
    exit();
}
?>



<?php
session_start();
include('connection.php');

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($user === '' || $pass === '') {
        $error = "Please fill out username and password.";
    } else {
        // Check if username already exists
        $checkSql = "SELECT id FROM users WHERE username = '$user' LIMIT 1";
        $checkRes = mysqli_query($conn, $checkSql);

        if (mysqli_num_rows($checkRes) > 0) {
            $error = "Username already exists!";
        } else {
// Create user
            // In your schema, roles mapping is:
            // roleId=1 admin, roleId=2 broker (partner dashboard), roleId=3 seller (partner dashboard)
            // We default new signups to roleId=2 (broker) -> partner dashboard.
            $roleId = 2;
            $insertSql = "INSERT INTO users (username, password, roleId) VALUES ('$user', '$pass', $roleId)";


            $insertRes = mysqli_query($conn, $insertSql);

            if ($insertRes) {
                // Auto-login newly created user
                $loginSql = "SELECT * FROM users WHERE username = '$user' AND password = '$pass'";
                $loginRes = mysqli_query($conn, $loginSql);

                if (mysqli_num_rows($loginRes) === 1) {
                    $row = mysqli_fetch_assoc($loginRes);
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['roleId'] = $row['roleId'];

                    // Success: return to login page (as requested)
                    // (login.php will redirect to admin_dashboard.php or partner_dashboard.php based on roleId)
                    header("Location: ../overall/dboard/login.html?signup=success");

                    // If you are serving dboard directly, you may also open ../dboard/login.html.
                    // (kept as-is to match your existing login redirect approach)

                    exit();

                }

                $error = "Account created but redirect failed.";
            } else {
                $error = "Failed to create account.";
            }
        }
    }
}


// If there was an error, show a simple message and send back to signup page
if ($error !== "") {
    echo "<script>alert(" . json_encode($error) . "); window.location.href = '../overall/signup.html';</script>";
    exit();
}
?>


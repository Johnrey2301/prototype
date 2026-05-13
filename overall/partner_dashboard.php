<?php
session_start();
if (!isset($_SESSION['username']) || ($_SESSION['roleId'] != 2 && $_SESSION['roleId'] != 3)) {
    header("Location: login.php");
    exit();
}
?>
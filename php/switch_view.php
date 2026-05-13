<?php
// Simple helper to redirect to correct HTML/PHP views.
// Usage: switch_view.php?role=1  (admin)
//        switch_view.php?role=2  (broker)
//        switch_view.php?role=3  (seller)

$role = isset($_GET['role']) ? (int)$_GET['role'] : 0;

if ($role === 1) {
    header("Location: admin_dashboard.php");
    exit();
}

header("Location: partner_dashboard.php");
exit();


<?php
session_start();
include("db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

// === User Creation ===
if (isset($_POST['add_user'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];
    $r = $_POST['role'];
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $u, $p, $r);
    $stmt->execute();
    $admin = $_SESSION['username'];
    $conn->query("INSERT INTO activity_log (username, activity) VALUES ('$admin', 'Added user $u with role $r')");
}

// === Delete User ===
if (isset($_POST['delete_user'])) {
    $del_user = $_POST['delete_user'];
    if ($del_user !== $_SESSION['username']) {
        $conn->query("DELETE FROM users WHERE username='$del_user'");
        $admin = $_SESSION['username'];
        $conn->query("INSERT INTO activity_log (username, activity) VALUES ('$admin', 'Deleted user $del_user')");
    }
}

// === Data Fetching ===
$users = $conn->query("SELECT * FROM users");
$logs = $conn->query("SELECT * FROM activity_log ORDER BY timestamp DESC");
$analytics = $conn->query("SELECT role, COUNT(*) as total FROM users GROUP BY role");
?>

<?php
session_start();
include("db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Branch') {
    header("Location: login.php");
    exit;
}

$branch = $_SESSION['username'];

// === Submit Item Request to StoreManager ===
if (isset($_POST['submit_request'])) {
    $item = $_POST['item_name'];
    $qty = $_POST['quantity'];

    $stmt = $conn->prepare("SELECT unit_price FROM inventory WHERE item_name = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $item);
    $stmt->execute();
    $result = $stmt->get_result();
    $inventory_item = $result->fetch_assoc();

    if ($inventory_item) {
        $price = $inventory_item['unit_price'];
        $stmt = $conn->prepare("INSERT INTO branch_request (item_name, quantity, price, status) VALUES (?, ?, ?, 'Pending')");
        if (!$stmt) {
            die("Insert Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("sid", $item, $qty, $price);
        $stmt->execute();
        $conn->query("INSERT INTO activity_log (username, activity) VALUES ('$branch', 'Requested $qty x $item to Store')");
    }
}
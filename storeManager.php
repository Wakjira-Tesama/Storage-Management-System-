<?php
session_start();
include("db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'StoreManager') {
    header("Location: login.php");
    exit;
}

$manager = $_SESSION['username'];

// === Add/Update Inventory ===
if (isset($_POST['add_item'])) {
    $name = $_POST['item_name'];
    $qty = $_POST['quantity'];
    $price = $_POST['unit_price'];
    $supplier = $_POST['supplier'];

    if (!filter_var($qty, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
        die("Quantity must be a positive integer.");
    }

    $stmt = $conn->prepare("INSERT INTO inventory (item_name, quantity, unit_price, supplier) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sids", $name, $qty, $price, $supplier);
    $stmt->execute();

    $manager = $_SESSION['username'];
    $conn->query("INSERT INTO activity_log (username, activity) VALUES ('$manager', 'Added item $name')");
}

<?php
session_start();
include("db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Buyer') {
    header("Location: login.php");
    exit;
}

$buyer = $_SESSION['username'];

// === Submit Buy Request ===
if (isset($_POST['buy_item'])) {
    $item = $_POST['item_name'];
    $qty = $_POST['quantity'];

    $stmt = $conn->prepare("SELECT unit_price FROM inventory WHERE item_name = ?");
    $stmt->bind_param("s", $item);
    $stmt->execute();
    $result = $stmt->get_result();
    $store_item = $result->fetch_assoc();

    if ($store_item) {
        $price = $store_item['unit_price'];
        $stmt = $conn->prepare("INSERT INTO branch_request (item_name, quantity, price, status, requester) VALUES (?, ?, ?, 'Pending', ?)");
        $stmt->bind_param("sids", $item, $qty, $price, $buyer);
        $stmt->execute();
    }
}
// === My Request History ===
$pending_requests = $conn->query("SELECT * FROM branch_request WHERE requester = '$buyer' AND status = 'Pending'");
$approved_requests = $conn->query("SELECT * FROM branch_request WHERE requester = '$buyer' AND status = 'Approved'");

// === My Branch Inventory View ===
$branch_inventory = $conn->query("SELECT item_name, quantity, unit_price FROM branch_inventory ORDER BY timestamp DESC");

// === Item Dropdown List ===
$item_list = $conn->query("SELECT item_name FROM branch_inventory");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Buyer Dashboard</title>
    <link rel="stylesheet" href="stylee.css">
    <style>
        .green-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .green-btn:hover {
            background-color: #218838;
        }
        .section {
            margin-bottom: 40px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        h3 {
            margin-bottom: 10px;
        }
    </style>
</head>
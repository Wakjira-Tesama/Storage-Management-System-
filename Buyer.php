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
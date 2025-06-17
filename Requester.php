<?php
session_start();
include("db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Requester') {
    header("Location: login.php");
    exit;
}

$requester = $_SESSION['username'];// === Submit New Request ===
if (isset($_POST['submit_request'])) {
    $item = $_POST['item_name'];
    $qty = $_POST['quantity'];
    $price = $_POST['price'];
    $supplier = $_POST['supplier'];
    if (!filter_var($qty, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
        die("Quantity must be a positive integer.");
    }
    $stmt = $conn->prepare("INSERT INTO item_requests (requester, item_name, quantity, price, supplier) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssids", $requester, $item, $qty, $price, $supplier);
    
    $stmt->execute();

    $conn->query("INSERT INTO activity_log (username, activity) VALUES ('$requester', 'Submitted request for $qty x $item at $price each')");
}


// === View All My Requests ===
$my_requests = $conn->query("SELECT * FROM item_requests WHERE requester = '$requester' ORDER BY timestamp DESC");
?>
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
<!DOCTYPE html>
<html>
<head>
    <title>Supplier Dashboard</title>
    <link rel="stylesheet" href="stylee.css">
</head>
<body>

<h2>Supplier Dashboard</h2>
<div class="logout">
    <p>Welcome, <?= $_SESSION['username'] ?> | <a href="logout.php">Logout</a></p>
</div>
<nav class="admin-nav">
    <ul>
        <li><a href="#" class="tab-link" data-target="newitem" onclick="showSection('newitem', this)">Submit New Item Request</a></li>
        <li><a href="#" class="tab-link" onclick="showSection('req', this)">My Request</a></li>
    </ul>
</nav>
<div id="newitem" class="section" >
<section>
    <h3>Submit New Item Request</h3>
    <form method="post">
        <input type="text" name="item_name" placeholder="Item Name" required>
        <input type="number" name="quantity" placeholder="Quantity" min="1" required>
        <input type="number" name="price" step="0.01" min="0" placeholder="Unit Price" required>
        <input type="text" name="supplier" placeholder="Supplier Name" required>
        <button type="submit" name="submit_request">Submit Request</button>
    </form>
</section>
</div>
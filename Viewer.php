<?php
session_start();
include("db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Viewer') {
    header("Location: login.php");
    exit;
}

$viewer = $_SESSION['username'];

// === Handle Suggestion ===
if (isset($_POST['suggest_item'])) {
    $item = $_POST['item_name'];
    $reason = $_POST['reason'];

    $stmt = $conn->prepare("INSERT INTO item_suggestions (viewer, item_name, reason) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $viewer, $item, $reason);
    $stmt->execute();

    $conn->query("INSERT INTO activity_log (username, activity) VALUES ('$viewer', 'Suggested item: $item')");
}

// === Fetch Inventory and Suggestions ===
$inventory = $conn->query("SELECT * FROM inventory ORDER BY quantity DESC");
$suggestions = $conn->query("SELECT * FROM item_suggestions WHERE viewer = '$viewer' ORDER BY timestamp DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Viewer Dashboard</title>
    <link rel="stylesheet" href="stylee.css">
    
</head>
<body>
<h2>Viewer Dashboard</h2>
<div class="logout">
    <p>Welcome, <?= $_SESSION['username'] ?> | <a href="logout.php">Logout</a></p>
</div>
<nav class="admin-nav">
    <ul>
        <li><a href="#" class="tab-link" data-target="AI" onclick="showSection('AI', this)">Available Inventory</a></li>
        <li><a href="#" class="tab-link" onclick="showSection('SI', this)">Suggest an Item</a></li>
        <li><a href="#" class="tab-link" onclick="showSection('MS', this)">My Suggestions</a></li>
    </ul>
</nav>
<section id="AI" class="section">
    <h3>Available Inventory</h3>
    <table>
        <tr><th>ID</th><th>Item Name</th><th>Quantity</th><th>Unit Price</th><th>Supplier</th></tr>
        <?php while($item = $inventory->fetch_assoc()): ?>
        <tr>
            <td><?= $item['id'] ?></td>
            <td><?= $item['item_name'] ?></td>
            <td><?= $item['quantity'] ?></td>
            <td><?= $item['unit_price'] ?></td>
            <td><?= $item['supplier'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</section>
<?php
include("db.php");

if (!isset($_GET['id'])) {
    die("Missing request ID.");
}

$request_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM branch_request WHERE id = ?");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data || $data['status'] !== 'Approved') {
    die("Invalid or unapproved request.");
}

$total = $data['quantity'] * $data['price'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approved Bill</title>
    <link rel="stylesheet" href="stylebill.css">
    
</head>
<body>

<h2>Store System</h2>
<h4>Ecommerce</h4>

<p><strong>Number:</strong> <?= $data['id'] ?><br>
<strong>Date:</strong> <?= date("Y-m-d") ?></p>

<table>
    <tr>
        <th>Item</th>
        <th>Quantity</th>
        <th>Rate</th>
        <th>Tax</th>
        <th>Amount</th>
    </tr>
    <tr>
        <td><?= $data['item_name'] ?></td>
        <td><?= $data['quantity'] ?></td>
        <td>$<?= number_format($data['price'], 2) ?></td>
        <td>$0.00</td>
        <td>$<?= number_format($total, 2) ?></td>
    </tr>
</table>

<table id="totals">
    <tr><td>Subtotal:</td><td>$<?= number_format($total, 2) ?></td></tr>
    <tr><td>Discount:</td><td>$0.00</td></tr>
    <tr><td>Tax:</td><td>$0.00</td></tr>
    <tr><td>Paid:</td><td>$0.00</td></tr>
</table>

<div class="highlight">
    Total: $<?= number_format($total, 2) ?>
</div>

</body>
</html>

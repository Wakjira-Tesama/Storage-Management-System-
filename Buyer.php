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
<body>

<h2>Buyer Dashboard</h2>
<p class="logout">Welcome, <?= $_SESSION['username'] ?> | <a href="logout.php">Logout</a></p>
<nav class="admin-nav">
    <ul>
        <li><a href="#" class="tab-link" data-target="tore" onclick="showSection('tore', this)">>My Branch Store</a></li>
        <li><a href="#" class="tab-link" onclick="showSection('bi', this)">Buy Item</a></li>
        <li><a href="#" class="tab-link" onclick="showSection('pr', this)">Pending Requests</a></li>
        <li><a href="#" class="tab-link" onclick="showSection('bill', this)">Approved Items - Bill</a></li>
    </ul>
</nav>
<section id="tore" class="section">
    <h3>My Branch Store</h3>
    <table>
        <tr><th>Item Name</th><th>Quantity</th><th>Price</th></tr>
        <?php while($row = $branch_inventory->fetch_assoc()): ?>
            <tr>
                <td><?= $row['item_name'] ?></td>
                <td><?= $row['quantity'] ?></td>
                <td><?= $row['unit_price'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</section>
<section id="bi" class="section">
    <h3>Buy Item</h3>
    <form method="post">
        <select name="item_name" required>
            <option value="">-- Select Item --</option>
            <?php while($item = $item_list->fetch_assoc()): ?>
                <option value="<?= $item['item_name'] ?>"><?= $item['item_name'] ?></option>
            <?php endwhile; ?>
        </select>
        <input type="number" name="quantity" min="1" placeholder="Quantity" required>
        <button type="submit" name="buy_item" class="green-btn">Buy</button>
    </form>
</section>

<section id="pr" class="section">
    <h3>Pending Requests</h3>
    <table>
        <tr><th>Item Name</th><th>Quantity</th><th>Price</th><th>Status</th></tr>
        <?php while($p = $pending_requests->fetch_assoc()): ?>
            <tr>
                <td><?= $p['item_name'] ?></td>
                <td><?= $p['quantity'] ?></td>
                <td><?= $p['price'] ?></td>
                <td><?= $p['status'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</section>
<section id="bill" class="section">
    <h3>Approved Items - Bill</h3>
    <table>
        <tr><th>Item Name</th><th>Quantity</th><th>Price</th><th>Total</th></tr>
        <?php 
        $total_amount = 0;
        while($a = $approved_requests->fetch_assoc()): 
            $line_total = $a['quantity'] * $a['price'];
            $total_amount += $line_total;
        ?>
            <tr>
               <td><a href="bill.php?id=<?= $a['id'] ?>" target="_blank"><?= $a['item_name'] ?></a></td>
               <td><?= $a['quantity'] ?></td>
               <td><?= $a['price'] ?></td>
                <td><?= number_format($line_total, 2) ?></td>
            </tr>

        <?php endwhile; ?>
        <tr><td colspan="3"><strong>Total</strong></td><td><strong><?= number_format($total_amount, 2) ?></strong></td></tr>
    </table>
</section>
<script src="script.js"></script>
</body>
</html>
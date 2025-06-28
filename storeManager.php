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


if (isset($_POST['delete_item'])) {
    $id = $_POST['item_id'];
    $conn->query("DELETE FROM inventory WHERE id=$id");
    $conn->query("INSERT INTO activity_log (username, activity) VALUES ('$manager', 'Deleted item ID $id')");
}
if (isset($_POST['update_item'])) {
    $id = $_POST['item_id'];
    $qty = $_POST['quantity'];
    $price = $_POST['unit_price'];

    if (!filter_var($qty, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
        die("Quantity must be a positive integer.");
    }

    $stmt = $conn->prepare("UPDATE inventory SET quantity=?, unit_price=? WHERE id=?");
    $stmt->bind_param("idi", $qty, $price, $id);
    $stmt->execute();

    $conn->query("INSERT INTO activity_log (username, activity) VALUES ('$manager', 'Updated quantity and price for item ID $id')");
}

if (isset($_POST['approve_request'])) {
    $id = $_POST['request_id'];

    // Get the request details
    $req_result = $conn->query("SELECT * FROM item_requests WHERE id = $id");
    $req = $req_result->fetch_assoc();

    if ($req) {
        $item = $req['item_name'];
        $qty = $req['quantity'];
        $price = $req['price'];
        $supplier = $req['supplier'];

        // Check if the same item with the same supplier exists
        $check = $conn->prepare("SELECT id, quantity FROM inventory WHERE item_name = ? AND supplier = ?");
        $check->bind_param("ss", $item, $supplier);
        $check->execute();
        $result = $check->get_result();

        if ($row = $result->fetch_assoc()) {
            // Item exists: update quantity
            $new_qty = $row['quantity'] + $qty;
            $stmt = $conn->prepare("UPDATE inventory SET quantity = ? WHERE id = ?");
            $stmt->bind_param("ii", $new_qty, $row['id']);
            $stmt->execute();

            $conn->query("INSERT INTO activity_log (username, activity) VALUES ('$manager', 'Approved request ID $id and updated quantity for existing item $item (Supplier: $supplier)')");
        } else {
            // Item doesn't exist: insert new row
            $stmt = $conn->prepare("INSERT INTO inventory (item_name, quantity, unit_price, supplier) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sids", $item, $qty, $price, $supplier);
            $stmt->execute();

            $conn->query("INSERT INTO activity_log (username, activity) VALUES ('$manager', 'Approved request ID $id and added new item $item (Supplier: $supplier)')");
        }

        // Mark the request as approved
        $conn->query("UPDATE item_requests SET status='Approved' WHERE id=$id");
    }
}


if (isset($_POST['reject_request'])) {
    $id = $_POST['request_id'];
    $conn->query("UPDATE item_requests SET status='Rejected' WHERE id=$id");
    $conn->query("INSERT INTO activity_log (username, activity) VALUES ('$manager', 'Rejected request ID $id')");
}

if (isset($_POST['approve_branch_request'])) {
    $id = $_POST['request_id'];

    $req = $conn->query("SELECT * FROM branch_request WHERE id = $id")->fetch_assoc();
    if ($req) {
        $item = $req['item_name'];
        $qty = $req['quantity'];
        $price = $req['price'];

        // Check current inventory
        $inv = $conn->query("SELECT quantity, unit_price, supplier FROM inventory WHERE item_name = '$item'")->fetch_assoc();
        if (!$inv || $inv['quantity'] < $qty) {
            // Reject if not enough stock
            $conn->query("UPDATE branch_request SET status='Rejected' WHERE id = $id");
            $conn->query("INSERT INTO activity_log (username, activity) VALUES ('$manager', 'Automatically rejected branch request ID $id due to insufficient stock')");
        } else {
            // Deduct from main inventory
            $conn->query("UPDATE inventory SET quantity = quantity - $qty WHERE item_name = '$item'");

            $unit_price = $inv['unit_price'];
            $supplier = $inv['supplier'];

            // ✅ Update or insert into branch_inventory
            $stmt = $conn->prepare("SELECT id, quantity FROM branch_inventory WHERE item_name = ? AND supplier = ?");
            $stmt->bind_param("ss", $item, $supplier);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $new_qty = $row['quantity'] + $qty;
                $update_stmt = $conn->prepare("UPDATE branch_inventory SET quantity = ?, unit_price = ?, timestamp = NOW() WHERE id = ?");
                $update_stmt->bind_param("dii", $new_qty, $unit_price, $row['id']);
                $update_stmt->execute();
            } else {
                $insert_stmt = $conn->prepare("INSERT INTO branch_inventory (item_name, quantity, unit_price, supplier, timestamp) VALUES (?, ?, ?, ?, NOW())");
                $insert_stmt->bind_param("sdds", $item, $qty, $unit_price, $supplier);
                $insert_stmt->execute();
            }

            $conn->query("UPDATE branch_request SET status='Approved' WHERE id = $id");
            $conn->query("INSERT INTO activity_log (username, activity) VALUES ('$manager', 'Approved branch request ID $id')");
        }
    }
}


if (isset($_POST['reject_branch_request'])) {
    $id = $_POST['request_id'];
    $conn->query("UPDATE branch_request SET status='Rejected' WHERE id = $id");
    $conn->query("INSERT INTO activity_log (username, activity) VALUES ('$manager', 'Rejected branch request ID $id')");
}

// === Fetch Data ===
$inventory = $conn->query("SELECT * FROM inventory");
$requests = $conn->query("SELECT * FROM item_requests WHERE status = 'Pending'");
$report = $conn->query("SELECT item_name, SUM(quantity) as total_requested FROM item_requests WHERE status='Approved' GROUP BY item_name");
$branch_requests = $conn->query("SELECT * FROM branch_request WHERE status = 'Pending'");
$branch_report = $conn->query(" SELECT item_name, SUM(quantity) as total_requested  FROM branch_request  WHERE status='Approved'  GROUP BY item_name");


?>

<!DOCTYPE html>
<html>
<head>
    <title>Store Manager Dashboard</title>
    <link rel="stylesheet" href="stylee.css">
    
</head>
<body>

<h2>Store Manager Dashboard</h2>
<div class="logout">
    <p>Welcome, <?= $_SESSION['username'] ?> | <a href="logout.php">Logout</a></p>
</div>
<nav class="admin-nav">
    <ul>
        <li><a href="#" class="tab-link" data-target="additem" onclick="showSection('additem', this)">Add Inventory Item</a></li>
        <li><a href="#" class="tab-link" onclick="showSection('inventory', this)">Current Inventory</a></li>
        <li><a href="#" class="tab-link" onclick="showSection('requests', this)">Pending Requests</a></li>
        <li><a href="#" class="tab-link" onclick="showSection('report', this)">Usage Report (Approved)</a></li>
    </ul>
</nav>


<section id="additem" class="section">
    <h3>Add Inventory Item</h3>
    <form method="post">
        <input type="text" name="item_name" placeholder="Item Name" required>
        <input type="number" name="quantity" placeholder="Quantity" min="1" required>
        <input type="text" name="unit_price" placeholder="Unit Price" required>
        <input type="text" name="supplier" placeholder="Supplier" required>
        <button type="submit" name="add_item">Add Item</button>
    </form>
</section>
<section id="inventory" class="section">
    <h3>Current Inventory</h3>
    <input type="text" id="searchInput" placeholder="Search by item name " onkeyup="filterUsers()" style="margin-bottom:10px; padding:5px; width:200px;">
    <div style="max-height: 300px; overflow-y: auto;">
    <table id="usersTable">
       <thead><tr><th>ID</th><th>Item Name</th><th>Quantity</th><th>Price</th><th>Supplier</th><th>Action</th><th>Action</th></tr> </thead> 
        <?php while($i = $inventory->fetch_assoc()): ?>
        <tr>
            <td><?= $i['id'] ?></td>
            <td><?= $i['item_name'] ?></td>
            <td><?= $i['quantity'] ?></td>
            <td><?= $i['unit_price'] ?></td>
            <td><?= $i['supplier'] ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="item_id" value="<?= $i['id'] ?>">
                    <button type="submit" name="delete_item" onclick="return confirm('Delete this item?')">Delete</button>
                </form>
            </td>
            <td>
    <form method="post" style="display:inline;">
        <input type="hidden" name="item_id" value="<?= $i['id'] ?>">
        <input type="number" name="quantity" min="1" placeholder="Quantity" value="<?= $i['quantity'] ?>" required>
        <input type="text" name="unit_price" min="1" placeholder="Unit Price" value="<?= $i['unit_price'] ?>" required>
        <button type="submit" name="update_item">Update</button>
    </form>
</td>

        </tr>
        <?php endwhile; ?>
    </table>
    </div>
</section>

<div id="requests" class="section">
<section>
    <h3>Pending Requests</h3>
    <table>
        <tr><th>ID</th><th>Requester</th><th>Item</th><th>Quantity</th><th>Time</th><th>Action</th></tr>
        <?php while($r = $requests->fetch_assoc()): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= $r['requester'] ?></td>
            <td><?= $r['item_name'] ?></td>
            <td><?= $r['quantity'] ?></td>
            <td><?= $r['timestamp'] ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                    <button type="submit" name="approve_request">Approve</button>
                </form>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                    <button type="submit" name="reject_request">Reject</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</section>

<section>
    <h3>Pending Branch Requests</h3>
    <table>
        <tr><th>ID</th><th>Item</th><th>Qty</th><th>Price</th><th>Time</th><th>Action</th></tr>
        <?php while($r = $branch_requests->fetch_assoc()): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= $r['item_name'] ?></td>
            <td><?= $r['quantity'] ?></td>
            <td><?= $r['price'] ?></td>
            <td><?= $r['timestamp'] ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                    <button type="submit" name="approve_branch_request">Approve</button>
                </form>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                    <button type="submit" name="reject_branch_request">Reject</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</section>

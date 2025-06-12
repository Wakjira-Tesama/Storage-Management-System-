<?php
session_start();
include("db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

// === User Creation ===
if (isset($_POST['add_user'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];
    $r = $_POST['role'];
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $u, $p, $r);
    $stmt->execute();
    $admin = $_SESSION['username'];
    $conn->query("INSERT INTO activity_log (username, activity) VALUES ('$admin', 'Added user $u with role $r')");
}

// === Delete User ===
if (isset($_POST['delete_user'])) {
    $del_user = $_POST['delete_user'];
    if ($del_user !== $_SESSION['username']) {
        $conn->query("DELETE FROM users WHERE username='$del_user'");
        $admin = $_SESSION['username'];
        $conn->query("INSERT INTO activity_log (username, activity) VALUES ('$admin', 'Deleted user $del_user')");
    }
}

// === Data Fetching ===
$users = $conn->query("SELECT * FROM users");
$logs = $conn->query("SELECT * FROM activity_log ORDER BY timestamp DESC");
$analytics = $conn->query("SELECT role, COUNT(*) as total FROM users GROUP BY role");
?>

<!DOCTYPE html>
<>
<head>
    <title>Admin</title>
    <link rel="stylesheet" href="stylee.css">
    
</head>
<body>

<h2 id="id">Admin Dashboard</h2>
<div class="logout">
    <p>Welcome, <?= $_SESSION['username'] ?> | <a href="logout.php">Logout</a></p>
</div>

<!-- Horizontal Navigation -->
<nav class="admin-nav">
    <ul>
        <li><a href="#" class="tab-link" data-target="account" onclick="showSection('account', this)">User Account Management</a></li>
        <li><a href="#" class="tab-link" onclick="showSection('users', this)">All Users</a></li>
        <li><a href="#" class="tab-link" onclick="showSection('analytics', this)">System Analytics</a></li>
        <li><a href="#" class="tab-link" onclick="showSection('logs', this)">Activity & Log Monitoring</a></li>
    </ul>
</nav>


<!-- Section: User Account Management -->
<section id="account" class="section">
    <h3>User Account Management</h3>
    <form method="post">
        <input type="text" name="username" placeholder="New Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="">Select Role</option>
            <option value="Admin">Admin</option>
            <option value="StoreManager">Store Manager</option>
            <option value="Requester">Requester</option>
            <option value="Viewer">Viewer</option>
            <option value="Branch">Branch</option>
        </select>
        <button type="submit" name="add_user">Add User</button>
    </form>
</section>

<!-- Section: All Users -->
<section id="users" class="section" style="display: none;">
    <h4>All Users</h4>
    <input type="text" id="searchInput" placeholder="Search by username..." onkeyup="filterUsers()" style="margin-bottom:10px; padding:5px; width:200px;">
    <div style="max-height: 300px; overflow-y: auto;">
        <table id="usersTable">
            <thead>
                <tr><th>ID</th><th>Username</th><th>Role</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php
                // Rerun query since previous result set was consumed
                $users = $conn->query("SELECT * FROM users");
                while($u = $users->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= $u['username'] ?></td>
                    <td><?= $u['role'] ?></td>
                    <td>
                        <?php if ($u['username'] !== $_SESSION['username']): ?>
                            <a href="?delete_user=<?= $u['username'] ?>" onclick="return confirm('Delete user?')">Delete</a>
                        <?php else: ?>
                            (You)
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>
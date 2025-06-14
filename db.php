<?php
$conn = new mysqli("localhost", "root", "", "store_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<?php
session_start();
include("db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Viewer') {
    header("Location: login.php");
    exit;
}

$viewer = $_SESSION['username'];

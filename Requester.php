<?php
session_start();
include("db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Requester') {
    header("Location: login.php");
    exit;
}

$requester = $_SESSION['username'];
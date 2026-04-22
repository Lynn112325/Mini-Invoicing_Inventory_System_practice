<?php
// includes/header.php
session_start();
if (!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mini invoicing & inventory system</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            min-height: 100vh;
            background: #343a40;
            color: #fff;
        }

        .nav-link {
            color: rgba(255, 255, 255, .75);
        }

        .nav-link:hover {
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block sidebar py-4">
                <h4 class="text-center mb-4">ERP System</h4>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="modules/products.php">Product Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="modules/suppliers.php">Suppliers</a></li>
                    <li class="nav-item"><a class="nav-link" href="modules/customers.php">Customer Data</a></li>
                    <hr class="bg-secondary">
                    <li class="nav-item"><a class="nav-link text-warning" href="modules/purchase.php">Purchase Inbound</a></li>
                    <li class="nav-item"><a class="nav-link text-info" href="modules/sales.php">Sales Outbound</a></li>
                    <hr class="bg-secondary">
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </nav>
            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
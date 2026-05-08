<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mini invoicing & inventory system</title>
    <script src="../public/assets/js/main.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
    <script src="../public/assets/js/apiErrorHandler.js"></script>
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
                    <li class="nav-item"><a class="nav-link" href="?route=dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="?route=product_list">Product Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="?route=supplier_list">Suppliers</a></li>
                    <li class="nav-item"><a class="nav-link" href="?route=customer_list">Customer Data</a></li>
                    <hr class="bg-secondary">
                    <li class="nav-item"><a class="nav-link text-warning" href="?route=purchase">Purchase Inbound</a></li>
                    <li class="nav-item"><a class="nav-link text-info" href="?route=sales">Sales Outbound</a></li>
                    <hr class="bg-secondary">
                    <li class="nav-item"><a class="nav-link" href="?route=logout">Logout</a></li>
                </ul>
            </nav>
            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
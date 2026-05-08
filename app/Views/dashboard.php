<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Products</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalProducts; ?> items</div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Low Stock Alerts</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $lowStock; ?> items</div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Today's Sales</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $todaySales; ?> orders</div>
            </div>
        </div>
    </div>
</div>

<hr>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">quick actions</h6>
            </div>
            <div class="card-body">
                <a href="?route=product_list" class="btn btn-primary">Manage Products</a>
                <a href="?route=purchase" class="btn btn-warning text-dark">Purchase Inventory</a>
                <a href="?route=sales" class="btn btn-info text-white">Sales Shipment</a>
            </div>
        </div>
    </div>
</div>
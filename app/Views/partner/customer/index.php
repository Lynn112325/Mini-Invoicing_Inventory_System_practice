<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Customer Management</h2>
        <a href="?route=customer_add" class="btn btn-primary">+ Add New Customer</a>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form method="GET">
                <input type="hidden" name="route" value="customer_list">

                <div class="row g-2 mb-3">
                    <div class="col-md-10">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control"
                                placeholder="Search by Name, Email or Phone..."
                                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100">Apply Filter</button>
                    </div>
                </div>

                <div class="row g-2 align-items-end">
                    <!-- Specific Filter: Customer Level -->
                    <div class="col-md-5">
                        <label class="form-label mb-1 fw-bold" style="font-size: 0.75rem;">CUSTOMER LEVEL</label>
                        <select name="level" class="form-select form-select-sm">
                            <option value="">All Levels</option>
                            <option value="normal" <?= ($_GET['level'] ?? '') == 'normal' ? 'selected' : '' ?>>Normal</option>
                            <option value="vip" <?= ($_GET['level'] ?? '') == 'vip' ? 'selected' : '' ?>>VIP</option>
                            <option value="vvep" <?= ($_GET['level'] ?? '') == 'vvep' ? 'selected' : '' ?>>VVEP</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1 fw-bold" style="font-size: 0.75rem;">CREDIT RANGE</label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="c_min" class="form-control" placeholder="Min" value="<?= htmlspecialchars($_GET['c_min'] ?? '') ?>">
                            <span class="input-group-text border-start-0 border-end-0 bg-transparent">-</span>
                            <input type="number" name="c_max" class="form-control" placeholder="Max" value="<?= htmlspecialchars($_GET['c_max'] ?? '') ?>">
                        </div>
                    </div>
                    <!-- Reset Link -->
                    <div class="col-md-1 text-center">
                        <a href="?route=customer_list" class="btn btn-sm btn-link text-decoration-none text-danger p-0 mb-1">
                            <i class="bi bi-x-circle me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <?= renderSortHeader('Name', 'name', $current_sort, $current_order); ?>
                            <th>Contact Info</th>
                            <?= renderSortHeader('Customer Level', 'level', $current_sort, $current_order); ?>
                            <?= renderSortHeader('Credit Limit', 'credit', $current_sort, $current_order); ?>
                            <?= renderSortHeader('Created At', 'date', $current_sort, $current_order); ?>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $c): ?>
                            <tr data-bs-toggle="collapse" data-bs-target="#customer-detail-<?= $c['id'] ?>" style="cursor: pointer;">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-chevron-right text-muted me-2 text-primary" id="icon-<?= $c['id'] ?>"></i>
                                        <div>
                                            <div class="fw-bold text-primary"><?= htmlspecialchars($c['name']) ?></div>
                                            <small class="text-muted">ID: #<?= $c['id'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($c['email']) ?></div>
                                    <div><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($c['phone']) ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $c['customer_level'] == 'vip' ? 'warning' : 'secondary' ?>">
                                        <?= strtoupper($c['customer_level']) ?>
                                    </span>
                                </td>
                                <td>$<?= number_format($c['credit_limit'], 2) ?></td>
                                <td><?= date('Y-m-d', strtotime($c['created_at'])) ?></td>
                                <td>
                                    <a href="?route=customer_edit&id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-info">Edit</a>
                                    <form action="?route=customer_delete" method="POST" style="display:inline;">
                                        <input type="hidden" name="delete_id" value="<?= $c['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this customer?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="bg-light">
                                <td colspan="6" class="p-0 border-0">
                                    <div class="collapse" id="customer-detail-<?= $c['id'] ?>">
                                        <div class="p-3 text-wrap">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="card card-body border-0 shadow-sm">
                                                        <h6 class="text-secondary mb-2">
                                                            <i class="bi bi-geo-alt me-2 text-danger"></i>Company Address
                                                        </h6>
                                                        <p class="mb-0 text-muted" style="line-height: 1.5;">
                                                            <?= !empty($c['address']) ? htmlspecialchars($c['address']) : 'No address provided.' ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?= renderPagination($total_pages, $current_page) ?>

            <div class="text-muted small">
                Showing <?= count($customers) ?> of <?= $result['total_results'] ?> customers
            </div>
        </div>
    </div>
</div>
<?php
if (isset($_SESSION['toast_success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast("<?= $_SESSION['toast_success'] ?>");
        });
    </script>
<?php
    unset($_SESSION['toast_success']);
endif; ?>
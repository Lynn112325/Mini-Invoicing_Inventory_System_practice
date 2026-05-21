<?php

/** 
 * @var array $suppliers
 * @var string $searchTerm
 * @var string $current_sort
 * @var string $current_order
 * @var int $total_pages
 * @var int $current_page
 * @var int $total_results
 */
?>
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Supplier Management</h2>
        <a href="?route=supplier_add" class="btn btn-primary">+ Add New Supplier</a>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form method="GET">
                <input type="hidden" name="route" value="supplier_list">

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
                    <!-- Specific Filter -->
                    <div class="col-md-11"></div>
                    <!-- Reset Link -->
                    <div class="col-md-1 text-center">
                        <a href="?route=supplier_list" class="btn btn-sm btn-link text-decoration-none text-danger p-0 mb-1">
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
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <?= renderSortHeader('Name', 'name', $current_sort, $current_order); ?>
                            <th>Contact Info</th>
                            <?= renderSortHeader('Tax ID', 'tax_id', $current_sort, $current_order); ?>
                            <?= renderSortHeader('Payment Terms', 'payment_terms', $current_sort, $current_order); ?>
                            <?= renderSortHeader('Created At', 'date', $current_sort, $current_order); ?>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($suppliers as $s): ?>
                            <tr>
                                <td data-bs-toggle="collapse" data-bs-target="#supplier-detail-<?= $s['id'] ?>" style="cursor: pointer;">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-chevron-right text-muted me-2 text-primary" id="icon-<?= $s['id'] ?>"></i>
                                        <div>
                                            <div class="fw-bold text-primary"><?= htmlspecialchars($s['name']) ?></div>
                                            <small class="text-muted">ID: #<?= $s['id'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><i class="bi bi-envelope me-1 text-muted"></i><?= htmlspecialchars($s['email']) ?></div>
                                    <div><i class="bi bi-telephone me-1 text-muted"></i><?= htmlspecialchars($s['phone']) ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?= strtoupper($s['tax_id']) ?></span>
                                </td>
                                <td>
                                    <?= htmlspecialchars($s['payment_terms']) ?>
                                </td>
                                <td><?= date('Y-m-d', strtotime($s['created_at'])) ?></td>
                                <!-- Actions btns -->
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li>
                                                <a class="dropdown-item" href="?route=supplier_edit&id=<?= $s['id'] ?>">
                                                    <i class="bi bi-pencil me-1"></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form action="?route=supplier_delete" method="POST" class="m-0 p-0">
                                                    <input type="hidden" name="delete_id" value="<?= $s['id'] ?>">
                                                    <button type="submit" class="dropdown-item text-danger"
                                                        onclick="return confirm('Are you sure you want to delete this supplier?');">
                                                        <i class="bi bi-trash me-1"></i> Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            <tr class="bg-light">
                                <td colspan="6" class="p-0 border-0">
                                    <div class="collapse" id="supplier-detail-<?= $s['id'] ?>">
                                        <div class="p-3 text-wrap">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="card card-body border-0 shadow-sm">
                                                        <h6 class="text-secondary mb-2">
                                                            <i class="bi bi-bank me-2 text-warning"></i>Bank Account Details
                                                        </h6>
                                                        <span class="font-monospace text-dark bg-light p-2 rounded border d-block">
                                                            <?= !empty($s['bank_account']) ? htmlspecialchars($s['bank_account']) : 'N/A' ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card card-body border-0 shadow-sm">
                                                        <h6 class="text-secondary mb-2">
                                                            <i class="bi bi-geo-alt me-2 text-danger"></i>Company Address
                                                        </h6>
                                                        <p class="mb-0 text-muted" style="line-height: 1.5;">
                                                            <?= !empty($s['address']) ? htmlspecialchars($s['address']) : 'No address provided.' ?>
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
                Showing <?= count($suppliers) ?> of <?= $total_results ?> suppliers
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
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Product Management</h2>
        <a href="?route=product_add" class="btn btn-primary">+ Add New Product</a>
    </div>
    <!-- filter -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body ">
            <form method="GET" id="filterForm">
                <input type="hidden" name="route" value="product_list">
                <!-- Search Box -->
                <div class="row g-2 mb-3">
                    <div class="col-md-10">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control"
                                placeholder="Search by Product Name or SKU..."
                                value="<?= htmlspecialchars($searchTerm) ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-funnel-fill me-1"></i> Apply Filter
                        </button>
                    </div>
                </div>
                <!-- Advanced Filters -->
                <div class="row g-2 align-items-end">
                    <!-- Category -->
                    <div class="col-md-3">
                        <label class="form-label mb-1 fw-bold" style="font-size: 0.75rem;">CATEGORY</label>
                        <select name="cat" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= (($_GET['cat'] ?? '') == $c['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Stock Range -->
                    <div class="col-md-4">
                        <label class="form-label mb-1 fw-bold" style="font-size: 0.75rem;">STOCK RANGE</label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="s_min" class="form-control" placeholder="Min" value="<?= htmlspecialchars($_GET['s_min'] ?? '') ?>">
                            <span class="input-group-text border-start-0 border-end-0 bg-transparent">-</span>
                            <input type="number" name="s_max" class="form-control" placeholder="Max" value="<?= htmlspecialchars($_GET['s_max'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="col-md-4">
                        <label class="form-label mb-1 fw-bold" style="font-size: 0.75rem;">PRICE RANGE ($)</label>
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.01" name="p_min" class="form-control" placeholder="Min" value="<?= htmlspecialchars($_GET['p_min'] ?? '') ?>">
                            <span class="input-group-text border-start-0 border-end-0 bg-transparent">-</span>
                            <input type="number" step="0.01" name="p_max" class="form-control" placeholder="Max" value="<?= htmlspecialchars($_GET['p_max'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- Reset Link -->
                    <div class="col-md-1 text-center">
                        <a href="?route=product_list" class="btn btn-sm btn-link text-decoration-none text-danger p-0 mb-1">
                            <i class="bi bi-x-circle me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <?= renderSortHeader('SKU', 'sku', $current_sort, $current_order); ?>
                            <?= renderSortHeader('Product Name', 'name', $current_sort, $current_order); ?>
                            <?= renderSortHeader('Category', 'category_name', $current_sort, $current_order); ?>
                            <?= renderSortHeader('Stock', 'stock_quantity', $current_sort, $current_order); ?>
                            <?= renderSortHeader('Unit Price', 'unit_price', $current_sort, $current_order); ?>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($products) > 0): ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><strong><?= $product['sku'] ?></strong></td>
                                    <td><?= $product['name'] ?></td>
                                    <td><?= $product['category_name'] ?? 'N/A' ?></td>
                                    <td><?= $product['stock_quantity'] ?></td>
                                    <td>$<?= number_format($product['unit_price'], 2) ?></td>
                                    <!-- Actions btns -->
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                <li>
                                                    <a class="dropdown-item" href="?route=product_edit&id=<?= $product['id'] ?>">
                                                        <i class="bi bi-pencil me-1"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="?route=product_delete" method="POST" class="m-0 p-0">
                                                        <input type="hidden" name="delete_id" value="<?= $product['id'] ?>">
                                                        <button type="submit" class="dropdown-item text-danger"
                                                            onclick="return confirm('Are you sure you want to delete this product?');">
                                                            <i class="bi bi-trash me-1"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No products found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?= renderPagination($total_pages, $current_page) ?>

            <div class="text-muted small">
                Showing <?= count($products) ?> of <?= $result['total_results'] ?> products
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
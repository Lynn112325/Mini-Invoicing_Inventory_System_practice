<?php

/** @var array $customer_levels */
?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h3>Add New Customer</h3>
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="customer_store.php" method="POST">
                        <?php include __DIR__ . '/../_partner_fields.php'; ?>
                        <hr>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Credit Limit</label>
                                <input type="number" name="credit_limit" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Customer Level</label>
                                <select name="customer_level" class="form-select">
                                    <?php foreach ($customer_levels as $level): ?>
                                        <option value="<?= htmlspecialchars($level) ?>"><?= htmlspecialchars($level) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button type="reset" class="btn btn-light">Reset</button>
                            <button type="submit" class="btn btn-primary px-4">
                                <?= isset($is_edit) && $is_edit ? 'Update Customer' : 'Save Customer' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
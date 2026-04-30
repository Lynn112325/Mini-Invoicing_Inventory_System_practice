<!-- Product Form Fields -->

<?php
function setErrorClass($field, $errors)
{
    return isset($errors[$field]) ? 'is-invalid' : '';
}

function displayError($field, $errors)
{
    if (isset($errors[$field])) {
        echo '<div class="invalid-feedback">' . $errors[$field] . '</div>';
    }
}
?>
<form method="POST">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">SKU (Stock Keeping Unit)</label>
            <input type="text" name="sku"
                class="form-control <?= setErrorClass('sku', $errors) ?>"
                value="<?= htmlspecialchars(html_entity_decode($product['sku'] ?? '')) ?>">
            <?php displayError('sku', $errors); ?>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" name="name"
                class="form-control <?= setErrorClass('name', $errors) ?>"
                value="<?= htmlspecialchars(html_entity_decode($product['name'] ?? '')) ?>">
            <?php displayError('name', $errors); ?>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Category</label>
        <div class="input-group">
            <select name="category_id" id="category_id"
                class="form-select <?= setErrorClass('category_id', $errors) ?>">
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"
                        <?= (isset($product['category_id']) && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars(html_entity_decode($cat['name'])) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="bi bi-plus-lg">+</i>
            </button>
            <?php displayError('category_id', $errors); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Initial Stock</label>
            <input type="number" name="stock_quantity" step="1" min="0"
                class="form-control <?= setErrorClass('stock_quantity', $errors) ?>"
                value="<?= htmlspecialchars(html_entity_decode($product['stock_quantity'] ?? '0')) ?>">
            <?php displayError('stock_quantity', $errors); ?>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Unit Price ($)</label>
            <input type="number" name="unit_price" step="0.01" min="0"
                class="form-control <?= setErrorClass('unit_price', $errors) ?>"
                value="<?= htmlspecialchars(html_entity_decode($product['unit_price'] ?? '0.00')) ?>">
            <?php displayError('unit_price', $errors); ?>
        </div>
    </div>

    <div class="text-end mt-3">
        <button type="reset" class="btn btn-light">Reset</button>
        <button type="submit" class="btn btn-primary px-4">
            <?= isset($is_edit) ? 'Update Product' : 'Save Product' ?>
        </button>
    </div>
</form>

<?php include '../category/_category_modal.php'; ?>
<script src="../../assets/js/category_handler.js"></script>
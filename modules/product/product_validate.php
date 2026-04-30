<?php
function validateProduct($data, $pdo, $productId = null)
{
    $errors = [];

    if (empty(trim($data['sku']))) $errors['sku'] = "SKU is required.";
    if (empty(trim($data['name']))) $errors['name'] = "Product Name is required.";
    if (empty($data['category_id'])) $errors['category_id'] = "Category is required.";

    if (!is_numeric($data['stock_quantity']) || $data['stock_quantity'] < 0) {
        $errors['stock_quantity'] = "Stock must be a non-negative number.";
    }
    if (!is_numeric($data['unit_price']) || $data['unit_price'] < 0) {
        $errors['unit_price'] = "Unit Price must be a non-negative number.";
    }

    // sku must be unique (exclude current product if editing)
    if (!isset($errors['sku'])) {
        $sql = "SELECT id FROM products WHERE sku = ? AND deleted_at IS NULL";
        $params = [$data['sku']];

        // if we're editing, exclude the current product ID from the check
        if ($productId) {
            $sql .= " AND id != ?";
            $params[] = $productId;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        if ($stmt->fetch()) {
            if (!$productId) $errors['sku'] = "This SKU is already taken.";
            else $errors['sku'] = "This SKU is already used by another product.";
        }
    }

    return $errors;
}

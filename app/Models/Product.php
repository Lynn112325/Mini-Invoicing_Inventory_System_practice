<?php
class Product
{
    private $db;
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function getCategories()
    {
        return $this->db->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll();
    }

    public function softDelete($id)
    {
        $stmt = $this->db->prepare("UPDATE products SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getPaginated($filters)
    {
        $conditions = ["p.deleted_at IS NULL"];
        $params = [];
        $filters = [
            'cat'   => ['col' => 'p.category_id',    'type' => 'int'],
            's_min' => ['col' => 'p.stock_quantity', 'op' => '>=', 'type' => 'int'],
            's_max' => ['col' => 'p.stock_quantity', 'op' => '<=', 'type' => 'int'],
            'p_min' => ['col' => 'p.unit_price',     'op' => '>=', 'type' => 'float'],
            'p_max' => ['col' => 'p.unit_price',     'op' => '<=', 'type' => 'float'],
        ];

        foreach ($filters as $key => $val) {
            if (isset($_GET[$key]) && $_GET[$key] !== '') {
                $op = $val['op'] ?? '=';
                $paramName = "filter_" . $key;
                $conditions[] = "{$val['col']} {$op} :{$paramName}";
                $params[$paramName] = ($val['type'] === 'int') ? (int)$_GET[$key] : (float)$_GET[$key];
            }
        }

        $fromWhereSql = "FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE " . implode(" AND ", $conditions);
        $selectSql = "SELECT p.*, c.name as category_name";
        $searchColumns = ['p.name', 'p.sku'];
        $allowedSort = ['sku', 'name', 'category_name', 'stock_quantity', 'unit_price', 'p.id'];

        require_once '../config/functions/pagination_helper.php';
        return getPaginatedData($this->db, $selectSql, $fromWhereSql, $searchColumns, $allowedSort, 'p.id', $params);
    }

    public function save($data)
    {
        $sql = "INSERT INTO products (sku, name, category_id, stock_quantity, unit_price, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
        return $this->db->prepare($sql)->execute([
            $data['sku'],
            $data['name'],

            $data['category_id'],
            $data['stock_quantity'],
            $data['unit_price']
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE products SET sku=?, name=?, category_id=?, stock_quantity=?, unit_price=? WHERE id=?";
        return $this->db->prepare($sql)->execute([
            $data['sku'],
            $data['name'],
            $data['category_id'],
            $data['stock_quantity'],
            $data['unit_price'],
            $id
        ]);
    }

    public function validate($data, $productId = null)
    {
        $errors = [];

        if (empty(trim($data['sku']))) $errors['sku'] = "SKU is required.";
        if (empty(trim($data['name']))) $errors['name'] = "Product is required.";

        if (!is_numeric($data['stock_quantity']) || $data['stock_quantity'] < 0) {
            $errors['stock_quantity'] = "Stock quantity must be a positive integer.";
        }

        if (!isset($errors['sku'])) {
            $sql = "SELECT id FROM products WHERE sku = ? AND deleted_at IS NULL";
            $params = [$data['sku']];
            if ($productId) {
                $sql .= " AND id != ?";
                $params[] = $productId;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            if ($stmt->fetch()) {
                $errors['sku'] = "This SKU is already in use.";
            }
        }

        return $errors;
    }
}

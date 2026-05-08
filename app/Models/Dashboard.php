<?php
class Dashboard
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function getStats()
    {
        return [
            'totalProducts' => $this->db->query("SELECT COUNT(*) FROM products WHERE deleted_at IS NULL")->fetchColumn(),
            'lowStock'      => $this->db->query("SELECT COUNT(*) FROM products WHERE stock_quantity < 10 AND deleted_at IS NULL")->fetchColumn(),
            'todaySales'    => $this->db->query("SELECT COUNT(*) FROM sales_orders WHERE DATE(order_date) = CURDATE()")->fetchColumn() ?: 0
        ];
    }
}

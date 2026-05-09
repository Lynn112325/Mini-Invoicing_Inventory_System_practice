<?php
class Category
{
    private $db;
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function exists($name)
    {
        $stmt = $this->db->prepare("SELECT id FROM categories WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetch() !== false;
    }

    public function create($name)
    {
        $stmt = $this->db->prepare("INSERT INTO categories (name) VALUES (?)");
        if ($stmt->execute([$name])) {
            return $this->db->lastInsertId();
        }
        return false;
    }
}

<?php
require_once '../../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Name is required']);
        exit;
    }

    try {
        // check for duplicate category name
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Category already exists']);
            exit;
        }

        // insert new category
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        if ($stmt->execute([$name])) {
            $newId = $pdo->lastInsertId();
            echo json_encode([
                'success' => true,
                'id' => $newId,
                'name' => $name
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

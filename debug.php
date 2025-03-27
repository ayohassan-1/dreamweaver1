<?php
require_once '../db.php';

try {
    $stmt = $pdo->query("DESCRIBE courses");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<pre>";
    print_r($columns);
    echo "</pre>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

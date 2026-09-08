<?php
require_once '../connect.php';

// Delete data from products table based on product ID
$productId = 1; // ID of the product to delete
$sql = "DELETE FROM products WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $productId]);
echo "Product deleted successfully!";
?>
<?php
require_once '../connect.php';

// Read data from products table
$sql = "SELECT * FROM products";
$stmt = $pdo->query($sql);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Display the products
foreach ($products as $product) {
    echo "ID: " . $product['id'] . "<br>";
    echo "Name: " . $product['name'] . "<br>";
    echo "Price: " . $product['price'] . "<br>";
    echo "Description: " . $product['description'] . "<br>";
    echo "Image: " . $product['image'] . "<br>";
    echo "Stock: " . $product['stock'] . "<br>";
    echo "Category: " . $product['category'] . "<br>";
    echo "<hr>";
}
?>
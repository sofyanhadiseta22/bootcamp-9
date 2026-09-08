<?php

require_once '../connect.php';

// Update data in products table (name, price, description, image, stock, category) based on product ID
$productId = 1; // ID of the product to update
$newName = "Updated Laptop";
$newPrice = 160000;
$newDescription = "An updated high-performance laptop suitable for gaming and professional work.";
$newImage = "updated_laptop.jpg";
$newStock = 15;
$newCategory = "Electronics";
//pastikan ada kondisi where data yang diambil yang mana misal id jdi id berapa yang mau diedit
$sql = "UPDATE products SET name = :name, price = :price, description = :description,image = :image, stock = :stock WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':name' => $newName,
    ':price' => $newPrice,
    ':description' => $newDescription,
    ':image' => $newImage,
    ':stock' => $newStock,
    ':id' => $productId
]);
echo "Product updated successfully!";
?>
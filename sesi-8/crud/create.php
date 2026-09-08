<?php
    require_once'../connect.php';
    // insert data into products table(name, price, description, image, stock, category)

    $name = "Laptop";
    $price = 15000;
    $description ="A high-performance laptop suitable for gaming and profesional work.";
    $image = "laptop.jpg";
    $stock = 10;
    $category = "Electronics";

   $sql = "INSERT INTO products (name, price, description, image, stock, category) 
    VALUES (:name, :price, :description, :image, :stock, :category)";
    // 
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':price' => $price,
        ':description' => $description,
        ':image' => $image,
        ':stock' => $stock,
        ':category' => $category
    ]);
    echo "New Product created successfully!";
?>
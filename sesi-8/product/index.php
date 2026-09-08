<?php
$pageTitle = "Beranda - WebDev App";
require_once __DIR__ . '/../connect.php';
require_once __DIR__ . '/../template/header.php';
require_once __DIR__ . '/../template/navbar.php';

$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="p-5 mb-4 bg-light rounded-3 border">
                <div class="container-fluid py-3">
                    <h1 class="display-5 fw-bold">Selamat Datang!</h1>
                    <p class="col-md-8 fs-4">Ini adalah template dasar PHP yang menggunakan Bootstrap 5.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<main class="container my-5">
    <h1 class="mb-4">Daftar Produk</h1>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Harga</th>

                <th>Deskripsi</th>
                <th>Gambar</th>
                <th>Stok</th>
                <th>Kategori</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['id']); ?></td>
                        <td><?= htmlspecialchars($product['name']); ?></td>
                        <td><?= htmlspecialchars($product['price']); ?></td>
                        <td><?= htmlspecialchars($product['description']); ?></td>
                        <td><img src="<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" width="100"></td>
                        <td><?= htmlspecialchars($product['stock']); ?></td>
                        <td><?= htmlspecialchars($product['category']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Belum ada produk.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<section class="container my-5" id="pelajari-lebih-lanjut">
    <div class="p-5 bg-light rounded-3 border text-center">
        <button class="btn btn-primary btn-lg mt-3" type="button">Pelajari Lebih Lanjut</button>
    </div>
</section>

<?php
require_once __DIR__ . '/../template/footer.php';
?>
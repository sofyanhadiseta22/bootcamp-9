<?php
session_start();

// Ambil error, data lama, dan pesan sukses dari session (jika ada)
$errors  = $_SESSION['errors'] ?? [];
$old     = $_SESSION['old'] ?? [];
$success = $_SESSION['success'] ?? '';

// Hapus session setelah diambil agar tidak muncul terus saat di-refresh
unset($_SESSION['errors'], $_SESSION['old'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Produk</title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Form Tambah Produk</h4>
                </div>

                <div class="card-body">

                    <form action="proses_produk.php"
                          method="POST"
                          enctype="multipart/form-data">

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Nama Produk
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="name"
                                name="name"
                                placeholder="Masukkan nama produk"
                                required
                            >
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                Deskripsi
                            </label>

                            <textarea
                                class="form-control"
                                id="description"
                                name="description"
                                rows="4"
                                placeholder="Masukkan deskripsi produk"
                                required
                            ></textarea>
                        </div>

                        <!-- Image -->
                        <div class="mb-3">
                            <label for="image" class="form-label">
                                Gambar Produk
                            </label>

                            <input
                                type="file"
                                class="form-control"
                                id="image"
                                name="image"
                                accept=".jpg,.jpeg,.png,.webp"
                                required
                            >

                            <div class="form-text">
                                Format: JPG, JPEG, PNG, WEBP. Maksimal 2 MB.
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="mb-3">
                            <label for="price" class="form-label">
                                Harga
                            </label>

                            <div class="input-group">
                                <span class="input-group-text">Rp</span>

                                <input
                                    type="number"
                                    class="form-control"
                                    id="price"
                                    name="price"
                                    placeholder="Masukkan harga"
                                    min="0"
                                    step="0.01"
                                    required
                                >
                            </div>
                        </div>

                        <!-- Stock -->
                        <div class="mb-3">
                            <label for="stock" class="form-label">
                                Stok
                            </label>

                            <input
                                type="number"
                                class="form-control"
                                id="stock"
                                name="stock"
                                placeholder="Masukkan jumlah stok"
                                min="0"
                                required
                            >
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="category" class="form-label">
                                Kategori
                            </label>

                            <select
                                class="form-select"
                                id="category"
                                name="category"
                                required
                            >
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Elektronik">Elektronik</option>
                                <option value="Fashion">Fashion</option>
                                <option value="Makanan">Makanan</option>
                                <option value="Minuman">Minuman</option>
                                <option value="Aksesoris">Aksesoris</option>
                            </select>
                        </div>

                        <!-- Button -->
                        <div class="d-flex gap-2">
                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Simpan Produk
                            </button>

                            <button
                                type="reset"
                                class="btn btn-secondary"
                            >
                                Reset
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
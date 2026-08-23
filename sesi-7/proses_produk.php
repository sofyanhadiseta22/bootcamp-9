<?php

// Cek apakah form dikirim menggunakan POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Akses tidak valid.");
}


// ========================================
// AMBIL DATA DARI FORM
// ========================================

$name = trim($_POST["name"] ?? "");
$description = trim($_POST["description"] ?? "");
$price = $_POST["price"] ?? "";
$stock = $_POST["stock"] ?? "";
$category = trim($_POST["category"] ?? "");


// ========================================
// ARRAY UNTUK MENYIMPAN ERROR
// ========================================

$errors = [];


// ========================================
// VALIDASI NAME
// ========================================

if ($name === "") {

    $errors[] = "Nama produk wajib diisi.";

} elseif (strlen($name) < 3) {

    $errors[] = "Nama produk minimal 3 karakter.";

} elseif (strlen($name) > 100) {

    $errors[] = "Nama produk maksimal 100 karakter.";
}


// ========================================
// VALIDASI DESCRIPTION
// ========================================

if ($description === "") {

    $errors[] = "Deskripsi produk wajib diisi.";

} elseif (strlen($description) < 10) {

    $errors[] = "Deskripsi minimal 10 karakter.";
}


// ========================================
// VALIDASI PRICE
// ========================================

if ($price === "") {

    $errors[] = "Harga wajib diisi.";

} elseif (!is_numeric($price)) {

    $errors[] = "Harga harus berupa angka.";

} elseif ($price < 0) {

    $errors[] = "Harga tidak boleh kurang dari 0.";
}


// ========================================
// VALIDASI STOCK
// ========================================

if ($stock === "") {

    $errors[] = "Stok wajib diisi.";

} elseif (!filter_var($stock, FILTER_VALIDATE_INT) && $stock !== "0") {

    $errors[] = "Stok harus berupa bilangan bulat.";

} elseif ($stock < 0) {

    $errors[] = "Stok tidak boleh kurang dari 0.";
}


// ========================================
// VALIDASI CATEGORY
// ========================================

$allowedCategories = [
    "Elektronik",
    "Fashion",
    "Makanan",
    "Minuman",
    "Aksesoris"
];

if ($category === "") {

    $errors[] = "Kategori wajib dipilih.";

} elseif (!in_array($category, $allowedCategories, true)) {

    $errors[] = "Kategori tidak valid.";
}


// ========================================
// VALIDASI IMAGE
// ========================================

if (!isset($_FILES["image"]) || $_FILES["image"]["error"] === UPLOAD_ERR_NO_FILE) {

    $errors[] = "Gambar produk wajib diupload.";

} elseif ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {

    $errors[] = "Terjadi kesalahan saat upload gambar.";

} else {

    $image = $_FILES["image"];

    // Maksimal 2 MB
    $maxSize = 2 * 1024 * 1024;

    if ($image["size"] > $maxSize) {
        $errors[] = "Ukuran gambar maksimal 2 MB.";
    }

    // Validasi MIME type
    $allowedMimeTypes = [
        "image/jpeg",
        "image/png",
        "image/webp"
    ];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $image["tmp_name"]);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedMimeTypes, true)) {
        $errors[] = "Format gambar harus JPG, JPEG, PNG, atau WEBP.";
    }
}


// ========================================
// JIKA ADA ERROR
// ========================================

if (!empty($errors)) {

    echo "<!DOCTYPE html>";
    echo "<html lang='id'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1'>";
    echo "<title>Validasi Produk</title>";

    echo "<link
        href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css'
        rel='stylesheet'
    >";

    echo "</head>";
    echo "<body class='bg-light'>";

    echo "<div class='container mt-5'>";
    echo "<div class='row justify-content-center'>";
    echo "<div class='col-md-8'>";

    echo "<div class='card shadow'>";
    echo "<div class='card-header bg-danger text-white'>";
    echo "<h4 class='mb-0'>Data Tidak Valid</h4>";
    echo "</div>";

    echo "<div class='card-body'>";

    echo "<div class='alert alert-danger'>";

    echo "<ul class='mb-0'>";

    foreach ($errors as $error) {
        echo "<li>" . htmlspecialchars($error) . "</li>";
    }

    echo "</ul>";

    echo "</div>";

    echo "<a href='tambah_produk.php' class='btn btn-primary'>";
    echo "Kembali ke Form";
    echo "</a>";

    echo "</div>";
    echo "</div>";

    echo "</div>";
    echo "</div>";
    echo "</div>";

    echo "</body>";
    echo "</html>";

    exit;
}


// ========================================
// UPLOAD GAMBAR
// ========================================

$uploadDirectory = "uploads/";

// Buat folder uploads jika belum ada
if (!is_dir($uploadDirectory)) {
    mkdir($uploadDirectory, 0755, true);
}


// Ambil ekstensi berdasarkan MIME type
$extensions = [
    "image/jpeg" => "jpg",
    "image/png"  => "png",
    "image/webp" => "webp"
];

$extension = $extensions[$mimeType];


// Buat nama file unik
$fileName = uniqid("produk_", true) . "." . $extension;

$filePath = $uploadDirectory . $fileName;


// Pindahkan file upload
if (!move_uploaded_file($image["tmp_name"], $filePath)) {

    die("Gagal menyimpan gambar.");
}


// ========================================
// DATA BERHASIL DIVALIDASI
// ========================================

// Untuk sementara data ditampilkan.
// Nantinya bagian ini dapat diganti dengan INSERT ke database.

echo "<!DOCTYPE html>";
echo "<html lang='id'>";
echo "<head>";

echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1'>";

echo "<title>Produk Berhasil</title>";

echo "<link
    href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css'
    rel='stylesheet'
>";

echo "</head>";

echo "<body class='bg-light'>";

echo "<div class='container mt-5'>";
echo "<div class='row justify-content-center'>";
echo "<div class='col-md-8'>";

echo "<div class='card shadow'>";

echo "<div class='card-header bg-success text-white'>";
echo "<h4 class='mb-0'>Produk Berhasil Disimpan</h4>";
echo "</div>";

echo "<div class='card-body'>";

echo "<div class='alert alert-success'>";
echo "Data produk berhasil divalidasi dan gambar berhasil diupload.";
echo "</div>";

echo "<table class='table table-bordered'>";

echo "<tr>";
echo "<th width='30%'>Nama</th>";
echo "<td>" . htmlspecialchars($name) . "</td>";
echo "</tr>";

echo "<tr>";
echo "<th>Deskripsi</th>";
echo "<td>" . nl2br(htmlspecialchars($description)) . "</td>";
echo "</tr>";

echo "<tr>";
echo "<th>Harga</th>";
echo "<td>Rp " . number_format((float)$price, 2, ",", ".") . "</td>";
echo "</tr>";

echo "<tr>";
echo "<th>Stok</th>";
echo "<td>" . htmlspecialchars($stock) . "</td>";
echo "</tr>";

echo "<tr>";
echo "<th>Kategori</th>";
echo "<td>" . htmlspecialchars($category) . "</td>";
echo "</tr>";

echo "<tr>";
echo "<th>Gambar</th>";
echo "<td>";

echo "<img
    src='" . htmlspecialchars($filePath) . "'
    alt='Gambar Produk'
    class='img-thumbnail'
    style='max-width: 200px;'
>";

echo "</td>";
echo "</tr>";

echo "</table>";

echo "<a href='tambah_produk.php' class='btn btn-primary'>";
echo "Tambah Produk Lagi";
echo "</a>";

echo "</div>";
echo "</div>";

echo "</div>";
echo "</div>";
echo "</div>";

echo "</body>";
echo "</html>";

?>
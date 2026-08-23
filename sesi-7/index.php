<?php
$nilai = 80;
$skor = "A";

$skor2 = $skor ?? "Tidak ada skor";
// Menggunakan null coalescing operator untuk memberikan nilai default jika $skor null

$skor2 = $skor == "A" ? "Lulus" : "Tidak Lulus";
// Menggunakan ternary operator untuk menentukan status kelulusan berdasarkan skor

if ($nilai >= 90) {
    if($nilai >= 95){
        $skor = "A+";
    } else {
        if($nilai >= 90){
            $skor = "A-";
        } else {
        $skor = "A";
        }
    }
} elseif ($nilai >= 80) {
    $skor = "B";
} elseif ($nilai >= 70) {
    $skor = "C";
} elseif ($nilai >= 60) {
    $skor = "D";
} else {
    $skor = "E";
}

echo "Nilai: $nilai, Skor: $skor";
?>

//http://localhost/bootcamp-9/bootcamp-9/sesi-7

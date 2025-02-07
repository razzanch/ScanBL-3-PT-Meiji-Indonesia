<?php
header("Content-Type: application/json"); // Mengatur header agar output berupa JSON

$servername = "localhost";  // Sesuaikan dengan konfigurasi database
$username = "root";  // Sesuaikan dengan username database
$password = "";  // Sesuaikan dengan password database
$database = "db_scanbl3";  // Nama database

// Membuat koneksi ke database
$conn = new mysqli($servername, $username, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Koneksi database gagal: " . $conn->connect_error]));
}

// Ambil data dari request POST
$product = isset($_POST['product']) ? trim($_POST['product']) : '';
$rss_code = isset($_POST['rss-code']) ? trim($_POST['rss-code']) : '';
$jam_code = isset($_POST['jam-code']) ? trim($_POST['jam-code']) : NULL; // Bisa NULL jika kosong

// Validasi input
if (empty($product) || empty($rss_code)) {
    echo json_encode(["success" => false, "message" => "Please fill all field."]);
    exit;
}

// Cek apakah kombinasi product dan rss_code sudah ada di database
$check_sql = "SELECT id_master FROM add_master WHERE product = ? OR rss_code = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ss", $product, $rss_code);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "The Combination of rss-code/product is already used."]);
    $check_stmt->close();
    $conn->close();
    exit;
}
$check_stmt->close();

// Query untuk insert data
$sql = "INSERT INTO add_master (product, rss_code, jam_code) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $product, $rss_code, $jam_code);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to add data: " . $conn->error]);
}

// Tutup koneksi database
$stmt->close();
$conn->close();
?>
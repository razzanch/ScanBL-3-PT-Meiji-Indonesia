<?php
session_start(); // Add this to access session variables
header("Content-Type: application/json");
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_scanbl3";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit;
}

// Pastikan request adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request method"]);
    exit;
}

// Periksa apakah data dikirim
if (empty($_POST)) {
    echo json_encode(["success" => false, "error" => "No POST data received"]);
    exit;
}

// Ambil data dari POST dan session
$product = $_POST['product'] ?? '';
$lot_number = $_POST['lot_number'] ?? '';
$date = $_POST['date'] ?? '';
$counter = $_POST['counter'] ?? '';
$operator = $_SESSION['username']; // Ambil username dari session

// Validasi input
if (!$product || !$lot_number || !$counter) {
    echo json_encode(["success" => false, "error" => "Missing required fields"]);
    exit;
}

// Ambil id_master dari add_master berdasarkan product
$sql = "SELECT id_master FROM add_master WHERE product = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $product);
$stmt->execute();
$stmt->bind_result($id_master);
$stmt->fetch();
$stmt->close();

if (!$id_master) {
    echo json_encode(["success" => false, "error" => "Product not found in add_master"]);
    exit;
}

// Update query untuk menyertakan operator
$stmt = $conn->prepare("INSERT INTO add_product (no_lot, counter, add_master_id_master, operator) VALUES (?, ?, ?, ?)");
$stmt->bind_param("siis", $lot_number, $counter, $id_master, $operator);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
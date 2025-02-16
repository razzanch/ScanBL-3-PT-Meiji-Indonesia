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

// Ambil data dari POST
$id_master = $_POST['id_master'] ?? '';

// Validasi input
if (!$id_master) {
    echo json_encode(["success" => false, "error" => "Missing required fields"]);
    exit;
}

// Update status di tabel add_master
$stmt = $conn->prepare("UPDATE add_master SET status = 'Active' WHERE id_master = ?");
$stmt->bind_param("i", $id_master);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
<?php
session_start();
header("Content-Type: application/json");

// Koneksi ke database
$host = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "db_scanbl3"; 

$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

// Ambil data dari request
$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

// Validasi input
if (empty($username) || empty($password)) {
    echo json_encode(['error' => 'Please Fill All Field']);
    exit;
}

// Cek username di database
$stmt = $conn->prepare("SELECT id_account, real_name, username, role, password FROM accounts WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    // Set session login
    $_SESSION['user_id'] = $user['id_account'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['real_name'] = $user['real_name'];
    $_SESSION['role'] = $user['role'];


    echo json_encode(['success' => true, 'user' => $user['username']]);
} else {
    echo json_encode(['error' => 'Wrong Password/Username!']);
}

// Tutup koneksi
$stmt->close();
$conn->close();
?>

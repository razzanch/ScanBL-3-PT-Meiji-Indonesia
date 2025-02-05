<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_scanbl3";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    $_SESSION['error'] = "Koneksi database gagal";
    header("Location: information-account.php?changepw_error=1");
    exit;
}

if (!isset($_SESSION['username'])) {
    $_SESSION['error'] = "User belum login";
    header("Location: ../login/login.php");
    exit;
}

// Ambil data dari form
$id_account = isset($_POST['id_account']) ? (int)$_POST['id_account'] : 0;
$current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
$new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';

// Validasi input
if (!$id_account || !$current_password || !$new_password) {
    $_SESSION['error'] = "Data tidak valid";
    header("Location: information-account.php?changepw_error=1");
    exit;
}

// Cek password saat ini
$check_query = "SELECT password FROM accounts WHERE id_account = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("i", $id_account);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row || !password_verify($current_password, $row['password'])) {
    $_SESSION['error'] = "Password saat ini salah";
    header("Location: information-account.php?changepw_error=1");
    exit;
}

// Hash password baru dan update database
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
$update_query = "UPDATE accounts SET password = ? WHERE id_account = ?";
$stmt = $conn->prepare($update_query);
$stmt->bind_param("si", $hashed_password, $id_account);

if ($stmt->execute()) {
    $_SESSION['success'] = "Password berhasil diubah";
    header("Location: information-account.php?changepw_success=1");
} else {
    $_SESSION['error'] = "Gagal mengubah password";
    header("Location: information-account.php?changepw_error=1");
}

$stmt->close();
$conn->close();
?>
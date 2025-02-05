<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_scanbl3";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    $_SESSION['error'] = "Koneksi database gagal";
    header("Location: information-account.php?editprofile_error=1");
    exit;
}

if (!isset($_SESSION['username'])) {
    $_SESSION['error'] = "User belum login";
    header("Location: ../login/login.php");
    exit;
}

// Ambil data dari form
$id_account = isset($_POST['id_account']) ? (int)$_POST['id_account'] : 0;
$real_name = isset($_POST['real_name']) ? trim($_POST['real_name']) : '';


// Validasi input
if (!$id_account || !$real_name) {
    $_SESSION['error'] = "Data tidak valid";
    header("Location: information-account.php?editprofile_error=1");
    exit;
}

// Update data di database
$update_query = "UPDATE accounts SET real_name = ? WHERE id_account = ?";
$stmt = $conn->prepare($update_query);
$stmt->bind_param("si", $real_name, $id_account);

if ($stmt->execute()) {
    $_SESSION['success'] = "Profil berhasil diperbarui";
    header("Location: information-account.php?editprofile_success=1");
} else {
    $_SESSION['error'] = "Gagal memperbarui profil";
    header("Location: information-account.php?editprofile_error=1");
}

$stmt->close();
$conn->close();
?>
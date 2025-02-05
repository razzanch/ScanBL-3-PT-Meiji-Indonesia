<?php
session_start();

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

// Ambil username dari session
$username = $_SESSION['username'];

// Koneksi database
$host = "localhost";
$username_db = "root";
$password_db = "";
$database = "db_scanbl3";

$conn = new mysqli($host, $username_db, $password_db, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari form
$id_master = isset($_POST['id_master']) ? (int)$_POST['id_master'] : 0;
$barcode = isset($_POST['barcode']) ? $conn->real_escape_string($_POST['barcode']) : '';
$product = isset($_POST['product']) ? $conn->real_escape_string($_POST['product']) : '';
$jam_code = isset($_POST['jam_code']) ? $conn->real_escape_string($_POST['jam_code']) : '';

// Validasi
if (!$id_master || !$barcode || !$product) {
    die("Data tidak valid.");
}

// Cek apakah barcode/RSS-code sudah ada di produk lain
$check_query = "
    SELECT id_master 
    FROM add_master 
    WHERE (rss_code = '$barcode' OR product = '$product')
    AND id_master != $id_master
";

$result = $conn->query($check_query);

if ($result->num_rows > 0) {
    // Jika barcode/RSS-code sudah ada di produk lain, kirim pesan error
    header("Location: product.php?update_error=1&message=Barcodes/RSS-codes are already used by other products");
    exit();
} else {
    // Update data di add_master
    $update_query = "
        UPDATE add_master 
        SET rss_code = '$barcode', 
            product = '$product', 
            jam_code = '$jam_code' 
        WHERE id_master = $id_master
    ";

    if ($conn->query($update_query)) {
        // **Update kolom operator di log_add_product hanya untuk data terbaru**
        $update_log_query = "
            UPDATE log_add_product 
            SET operator = '$username' 
            WHERE Change_Date IN (
                SELECT * FROM (
                    SELECT MAX(Change_Date) 
                    FROM log_add_product 
                    WHERE Barcode = '$barcode' AND Product = '$product' 
                    GROUP BY Barcode, Product
                ) AS subquery
            )
        ";

        $conn->query($update_log_query);

        header("Location: product.php?update_success=1");
        exit();
    } else {
        header("Location: product.php?update_error=1&message=Gagal mengupdate produk");
        exit();
    }
    
}

$conn->close();
?>

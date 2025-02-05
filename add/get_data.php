<?php
header("Content-Type: application/json");
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_scanbl3";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Jika tidak ada parameter 'product', ambil daftar produk
if (!isset($_GET['product']) && !isset($_GET['lot_number'])) {
    $sql = "SELECT DISTINCT product FROM add_master";
    $result = $conn->query($sql);

    if (!$result) {
        echo json_encode(["error" => "Query error: " . $conn->error]);
        exit;
    }

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row['product'];
    }

    echo json_encode($products);
    exit;
}

// Jika ada parameter 'product' saja, ambil detailnya
if (isset($_GET['product']) && !isset($_GET['lot_number'])) {
    $product = $_GET['product'];

    $sql = "SELECT id_master, rss_code, jam_code FROM add_master WHERE product = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(["error" => "Prepare statement failed"]);
        exit;
    }

    $stmt->bind_param("s", $product);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            "rss_code" => $row['rss_code'],
            "jam_code" => $row['jam_code'],
            "id_master" => $row['id_master']
        ]);
    } else {
        echo json_encode(["error" => "Product not found"]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// Jika ada parameter 'lot_number', ambil counter terakhir
if (isset($_GET['lot_number'])) {
    $lotNumber = $_GET['lot_number'];

    $sql = "SELECT ap.counter 
            FROM add_product ap
            WHERE ap.no_lot = ?
            ORDER BY ap.id_add DESC LIMIT 1";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(["error" => "Prepare statement failed"]);
        exit;
    }

    $stmt->bind_param("s", $lotNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(["last_counter" => $row['counter']]);
    } else {
        echo json_encode(["last_counter" => 0]); // Default jika tidak ditemukan
    }

    $stmt->close();
    $conn->close();
}
?>
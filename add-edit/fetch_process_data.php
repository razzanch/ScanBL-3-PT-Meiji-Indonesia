<?php
$host = "localhost";
$username_db = "root";
$password_db = "";
$database = "db_scanbl3";

// Koneksi ke database
$conn = new mysqli($host, $username_db, $password_db, $database);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Cek jika product & no_lot ada dalam request
if (isset($_GET['product']) && isset($_GET['no_lot'])) {
    $product = $conn->real_escape_string($_GET['product']);
    $no_lot = $conn->real_escape_string($_GET['no_lot']);

    // Fetch data dari add_master berdasarkan product
    $queryMaster = "SELECT product, rss_code, jam_code FROM add_master WHERE product = '$product' LIMIT 1";
    $resultMaster = $conn->query($queryMaster);
    
    // Fetch data dari add_product berdasarkan no_lot
    $queryProduct = "SELECT no_lot, counter FROM add_product WHERE no_lot = '$no_lot' ORDER BY date DESC LIMIT 1";
    $resultProduct = $conn->query($queryProduct);

    // Data awal
    $data = [
        "status" => "success",
        "product" => "",
        "rss_code" => "",
        "jam_code" => "",
        "no_lot" => "",
        "counter" => ""
    ];

    // Jika ada data dari add_master
    if ($resultMaster->num_rows > 0) {
        $rowMaster = $resultMaster->fetch_assoc();
        $data["product"] = $rowMaster["product"];
        $data["rss_code"] = $rowMaster["rss_code"];
        $data["jam_code"] = $rowMaster["jam_code"];
    }

    // Jika ada data dari add_product
    if ($resultProduct->num_rows > 0) {
        $rowProduct = $resultProduct->fetch_assoc();
        $data["no_lot"] = $rowProduct["no_lot"];
        $data["counter"] = $rowProduct["counter"];
    }

    echo json_encode($data);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}

$conn->close();
?>

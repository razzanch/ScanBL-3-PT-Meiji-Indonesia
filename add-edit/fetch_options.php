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

// Ambil action dari request
$action = isset($_GET['action']) ? $_GET['action'] : '';
$gedung = isset($_GET['gedung']) ? $_GET['gedung'] : ''; // Ambil gedung dari request

if ($action === 'getProducts') {
    if (!empty($gedung)) {
        // Query dengan filter berdasarkan gedung
        $query = "SELECT DISTINCT product FROM add_master WHERE gedung = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $gedung);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        // Query tanpa filter (tampilkan semua produk)
        $query = "SELECT DISTINCT product FROM add_master";
        $result = $conn->query($query);
    }

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row['product'];
    }
    echo json_encode($products);
}

if ($action === 'getNoLots') {
    $query = "SELECT DISTINCT no_lot FROM add_product";
    $result = $conn->query($query);

    $noLots = [];
    while ($row = $result->fetch_assoc()) {
        $noLots[] = $row['no_lot'];
    }
    echo json_encode($noLots);
}

if ($action === 'getCounter' && isset($_GET['no_lot'])) {
    $no_lot = $conn->real_escape_string($_GET['no_lot']);
    
    $query = "
        SELECT counter 
        FROM add_product 
        WHERE no_lot = '$no_lot'
        ORDER BY date DESC LIMIT 1
    ";
    
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    echo json_encode(["counter" => $row ? $row['counter'] : ""]);
}

$conn->close();
?>

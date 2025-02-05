<?php
session_start();
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_scanbl3";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        'status' => 'error', 
        'message' => 'Connection failed: ' . $conn->connect_error
    ]));
}

// Check if POST data is set
if (!isset($_POST['product']) || !isset($_POST['nolot'])) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Missing product or lot number'
    ]);
    exit;
}

$product_id = $_POST['product'];
$lot_number = $_POST['nolot'];

// Prepare SQL to check if the combination exists
$sql = "SELECT * FROM add_product 
        WHERE no_lot = ? 
        AND add_master_id_master = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $lot_number, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Set session variables before returning success
    $_SESSION['selected_product'] = $product_id;
    $_SESSION['selected_lot'] = $lot_number;

    // Data found
    echo json_encode([
        'status' => 'success', 
        'message' => 'Data found'
    ]);
} else {
    // Data not found
    echo json_encode([
        'status' => 'error', 
        'message' => 'Data not found'
    ]);
}

$stmt->close();
$conn->close();
?>
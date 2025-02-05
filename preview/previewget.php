<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure clean JSON output
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_scanbl3";

try {
    // Establish connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
    if (!$conn) {
        throw new Exception("Database connection failed: " . mysqli_connect_error());
    }

    // Fetch Products
    if (isset($_GET['action']) && $_GET['action'] == 'get_products') {
        $query = "SELECT DISTINCT am.product, am.id_master 
                  FROM add_master am 
                  JOIN add_product ap ON am.id_master = ap.add_master_id_master";
        
        $result = mysqli_query($conn, $query);
        
        if (!$result) {
            throw new Exception("Query failed: " . mysqli_error($conn));
        }

        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }

        echo json_encode(['status' => 'success', 'data' => $products]);
        exit;
    }

    // Fetch Lot Numbers
    if (isset($_GET['action']) && $_GET['action'] == 'get_lots') {
        if (!isset($_GET['product'])) {
            throw new Exception("Product is required");
        }

        $product = $_GET['product'];
        $query = "SELECT DISTINCT no_lot 
                  FROM add_product 
                  WHERE add_master_id_master = ?";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $product);
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        
        if (!$result) {
            throw new Exception("Query failed: " . mysqli_error($conn));
        }

        $lots = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $lots[] = $row['no_lot'];
        }

        echo json_encode(['status' => 'success', 'data' => $lots]);
        exit;
    }

    throw new Exception("Invalid action");

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage()
    ]);
    exit;
} finally {
    // Close connection if open
    if (isset($conn)) {
        mysqli_close($conn);
    }
}
?>
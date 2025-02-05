<?php 
header('Content-Type: application/json'); 

// Database connection 
$host = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "db_scanbl3"; 

$conn = new mysqli($host, $username, $password, $database); 

// Check connection 
if ($conn->connect_error) { 
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error])); 
} 

try { 
    $response = array(); 
     
// Fetch total scans today 
$today = date('Y-m-d'); 
// Modified query for better performance and reliability
$query = "SELECT COUNT(*) as total_scans  
          FROM add_product  
          WHERE date >= CURDATE() 
          AND date < CURDATE() + INTERVAL 1 DAY";
$result = $conn->query($query);
$response['total_scans'] = $result->fetch_assoc()['total_scans'];

    // Fetch active lot numbers 
    $query = "SELECT COUNT(DISTINCT no_lot) as active_lots  
              FROM add_product  
              WHERE date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    $result = $conn->query($query); 
    $response['active_lots'] = $result->fetch_assoc()['active_lots']; 

    // Fetch active products 
    $query = "SELECT COUNT(*) as active_products  
              FROM add_master";
    $result = $conn->query($query); 
    $response['active_products'] = $result->fetch_assoc()['active_products'];

    // Fetch monthly scan activity 
    $query = "SELECT  
                DATE_FORMAT(date, '%Y-%m') as month,
                COUNT(*) as scan_count, 
                COUNT(DISTINCT no_lot) as unique_lots 
              FROM add_product 
              WHERE date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
              GROUP BY DATE_FORMAT(date, '%Y-%m')
              ORDER BY month"; 
    $result = $conn->query($query); 
    $monthly_data = array(); 
    while ($row = $result->fetch_assoc()) { 
        $monthly_data[] = $row; 
    } 
    $response['monthly_data'] = $monthly_data; 

    // Fetch product distribution 
    $query = "SELECT  
                m.product, 
                COUNT(p.id_add) as scan_count, 
                m.rss_code,
                m.jam_code
              FROM add_master m 
              JOIN add_product p ON m.id_master = p.add_master_id_master 
              WHERE p.date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
              GROUP BY m.product, m.rss_code, m.jam_code 
              ORDER BY scan_count DESC
              LIMIT 10"; 
    $result = $conn->query($query); 
    $product_data = array(); 
    while ($row = $result->fetch_assoc()) { 
        $product_data[] = $row; 
    } 
    $response['product_data'] = $product_data; 

    // Fetch log activity trends
    $query = "SELECT 
                DATE_FORMAT(Change_Date, '%Y-%m-%d') as date,
                Change_Type,
                COUNT(*) as count
              FROM log_add_product
              WHERE Change_Date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
              GROUP BY DATE_FORMAT(Change_Date, '%Y-%m-%d'), Change_Type
              ORDER BY date";
    $result = $conn->query($query);
    $log_trend_data = array();
    while ($row = $result->fetch_assoc()) {
        $log_trend_data[] = $row;
    }
    $response['log_trend_data'] = $log_trend_data;

    // Add error checking for empty datasets
    if (empty($monthly_data)) {
        $response['warnings'][] = "No monthly scan data available";
    }
    if (empty($product_data)) {
        $response['warnings'][] = "No product distribution data available";
    }
    if (empty($log_trend_data)) {
        $response['warnings'][] = "No log trend data available";
    }

    // Return all data as JSON
    echo json_encode($response); 

} catch (Exception $e) { 
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'stack_trace' => $e->getTraceAsString()
    ]); 
} finally { 
    // Close the connection 
    $conn->close(); 
} 
?>
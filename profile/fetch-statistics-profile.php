<?php
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

// Get username from request
$username = $_GET['username'] ?? '';

// Prepare response array
$response = [];

// 1. Hourly Activity Distribution
$hourlyQuery = "
    SELECT 
        HOUR(l.Date) as hour,
        COUNT(*) as activity_count
    FROM log_add_product l
    WHERE l.operator = ?
    AND DATE(l.Date) = CURDATE()
    GROUP BY HOUR(l.Date)
    ORDER BY hour
";

$stmt = $conn->prepare($hourlyQuery);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$hourlyData = [];
while ($row = $result->fetch_assoc()) {
    $hourlyData[] = [
        'hour' => $row['hour'],
        'count' => $row['activity_count']
    ];
}
$response['hourlyActivity'] = $hourlyData;

// 2. Change Type Distribution
$changeTypeQuery = "
    SELECT 
        Change_Type,
        COUNT(*) as type_count
    FROM log_add_product
    WHERE operator = ?
    AND DATE(Date) = CURDATE()
    GROUP BY Change_Type
";

$stmt = $conn->prepare($changeTypeQuery);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$changeTypeData = [];
while ($row = $result->fetch_assoc()) {
    $changeTypeData[] = [
        'type' => $row['Change_Type'],
        'count' => $row['type_count']
    ];
}
$response['changeTypeDistribution'] = $changeTypeData;

// 3. Daily Progress
$dailyProgressQuery = "
    SELECT 
        HOUR(a.date) as hour,
        COUNT(DISTINCT a.no_lot) as active_lots,
        COUNT(*) as total_products
    FROM add_product a
    WHERE a.operator = ?
    AND DATE(a.date) = CURDATE()
    GROUP BY HOUR(a.date)
    ORDER BY hour
";

$stmt = $conn->prepare($dailyProgressQuery);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$dailyProgressData = [];
while ($row = $result->fetch_assoc()) {
    $dailyProgressData[] = [
        'hour' => $row['hour'],
        'activeLots' => $row['active_lots'],
        'totalProducts' => $row['total_products']
    ];
}
$response['dailyProgress'] = $dailyProgressData;

// Close connection
$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
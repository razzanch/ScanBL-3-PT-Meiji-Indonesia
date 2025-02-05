<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "db_scanbl3";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search parameter
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Get page number, default to page 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$limit = 25; // Number of records per page
$offset = ($page - 1) * $limit; // Calculate offset for query

// Base query conditions
$where_clause = $search ? "WHERE Barcode LIKE '%$search%' OR Product LIKE '%$search%' OR Jam_Code LIKE '%$search%'" : "";

// Query to get total number of records
$total_query = "
    SELECT COUNT(*) AS total 
    FROM log_add_product
    $where_clause
";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_data = $total_row['total'];

// Calculate total pages
$total_pages = ceil($total_data / $limit);

// Query to get data with LIMIT and OFFSET
$query = "
    SELECT 
        id_log AS LogID,
        Barcode,
        Product,
        Jam_Code,
        Date,
        Counter,
        No_Lot,
        Change_Type,
        Change_Date,
        Operator
    FROM log_add_product
    $where_clause
    ORDER BY Change_Date DESC
    LIMIT $limit OFFSET $offset
";

$result = $conn->query($query);

// Display table data
if ($result->num_rows > 0) {
    $no = $offset + 1; // Line number starting from offset + 1
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $no . "</td>";
        echo "<td>" . (!empty($row['Barcode']) ? htmlspecialchars($row['Barcode']) : "-") . "</td>";
        echo "<td>" . (!empty($row['Product']) ? htmlspecialchars($row['Product']) : "-") . "</td>";
        echo "<td>" . (!empty($row['Jam_Code']) ? htmlspecialchars($row['Jam_Code']) : "-") . "</td>";
        echo "<td>" . (!empty($row['Date']) ? htmlspecialchars($row['Date']) : "-") . "</td>";
        echo "<td>" . (!empty($row['Counter']) ? htmlspecialchars($row['Counter']) : "-") . "</td>";
        echo "<td>" . (!empty($row['No_Lot']) ? htmlspecialchars($row['No_Lot']) : "-") . "</td>";
        echo "<td>" . (!empty($row['Change_Type']) ? htmlspecialchars($row['Change_Type']) : "-") . "</td>";
        echo "<td>" . (!empty($row['Change_Date']) ? htmlspecialchars($row['Change_Date']) : "-") . "</td>";
        echo "<td>" . (!empty($row['Operator']) ? htmlspecialchars($row['Operator']) : "-") . "</td>";
        echo "</tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='9'>Data tidak ditemukan</td></tr>";
}

$conn->close();
?>

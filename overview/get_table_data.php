<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Pastikan session hanya dimulai jika belum aktif
}

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

// Ambil username dari session
$username = $_SESSION['username'];

// Database configuration
$host = "localhost";
$username_db = "root";
$password_db = "";
$database = "db_scanbl3";

$conn = new mysqli($host, $username_db, $password_db, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete request
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $delete_id = $conn->real_escape_string($_GET['id']);

    // Ambil data Barcode dan Product sebelum dihapus
    $select_query = "
        SELECT am.rss_code AS Barcode, am.product AS Product 
        FROM add_product AS ap
        INNER JOIN add_master AS am ON ap.add_master_id_master = am.id_master
        WHERE ap.id_add = '$delete_id'
    ";
    $result = $conn->query($select_query);
    $row = $result->fetch_assoc();
    $barcode = $row['Barcode'];
    $product = $row['Product'];

    $delete_query = "DELETE FROM add_product WHERE id_add = '$delete_id'";

    if ($conn->query($delete_query) === TRUE) {
        // Update log_add_product untuk mencatat operator
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

        header("Location: overview.php?delete_success=1");
        exit();
    } else {
        header("Location: overview.php?delete_error=1");
        exit();
    }
}

// Get search parameter
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Get page number, default to page 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$limit = 25; // Number of records per page
$offset = ($page - 1) * $limit; // Calculate offset for query

// Base query conditions
$where_clause = $search ? "WHERE am.rss_code LIKE '%$search%' OR am.product LIKE '%$search%' OR am.jam_code LIKE '%$search%'" : "";

// Query to get total number of records
$total_query = "
    SELECT COUNT(*) AS total 
    FROM add_product AS ap
    INNER JOIN add_master AS am 
    ON ap.add_master_id_master = am.id_master
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
        ap.id_add AS id,
        am.rss_code AS Barcode,
        am.product AS Product,
        am.jam_code AS JamCode,
        ap.date AS Date,
        ap.counter AS Counter,
        ap.no_lot AS NoLot
    FROM add_product AS ap
    INNER JOIN add_master AS am 
        ON ap.add_master_id_master = am.id_master
    $where_clause
    ORDER BY ap.date DESC
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
        echo "<td>" . (!empty($row['JamCode']) ? htmlspecialchars($row['JamCode']) : "-") . "</td>";
        echo "<td>" . (!empty($row['Date']) ? htmlspecialchars($row['Date']) : "-") . "</td>";
        echo "<td>" . (!empty($row['Counter']) ? htmlspecialchars($row['Counter']) : "-") . "</td>";
        echo "<td>" . (!empty($row['NoLot']) ? htmlspecialchars($row['NoLot']) : "-") . "</td>";
        
        // Add Action column with delete icon
        echo "<td class='action-column'>";
        echo "<a href='#' class='delete-btn' data-id='" . $row['id'] . "'>";
        echo "<img src='../assets/trash.png' alt='Delete' class='delete-icon'>";
        echo "</a>";
        echo "</td>";
        
        echo "</tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='8'>Data tidak ditemukan</td></tr>";
}

$conn->close();
?>

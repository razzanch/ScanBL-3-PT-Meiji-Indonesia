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

    // Ambil data Barcode, Product, dan id_master sebelum dihapus
    $select_query = "
        SELECT am.rss_code AS Barcode, am.product AS Product, am.id_master AS id_master 
        FROM add_product AS ap
        INNER JOIN add_master AS am ON ap.add_master_id_master = am.id_master
        WHERE ap.id_add = '$delete_id'
    ";
    $result = $conn->query($select_query);
    $row = $result->fetch_assoc();
    $barcode = $row['Barcode'];
    $product = $row['Product'];
    $id_master = $row['id_master'];

    // Hapus data dari tabel add_product
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

        // Cek apakah masih ada data product terkait di tabel add_product
        $check_product_query = "
            SELECT COUNT(*) AS total 
            FROM add_product 
            WHERE add_master_id_master = '$id_master'
        ";
        $check_result = $conn->query($check_product_query);
        $check_row = $check_result->fetch_assoc();
        $total_remaining = $check_row['total'];

        // Jika tidak ada data lagi, update status di tabel add_master menjadi "Inactive"
        if ($total_remaining == 0) {
            $update_status_query = "
                UPDATE add_master 
                SET status = 'Inactive' 
                WHERE id_master = '$id_master'
            ";
            $conn->query($update_status_query);
        }

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
        am.gedung AS gedung,
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
        echo "<td>" . (!empty($row['gedung']) ? htmlspecialchars($row['gedung']) : "-") . "</td>";
        
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
<?php
session_start(); // Pastikan session dimulai sebelum mengakses $_SESSION

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

// Ambil username dari session
$username = $_SESSION['username'];

$host = "localhost";
$username_db = "root";
$password_db = "";
$database = "db_scanbl3";

$conn = new mysqli($host, $username_db, $password_db, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_master = isset($_POST['id_master']) ? (int)$_POST['id_master'] : 0;
    $actual_product_name = isset($_POST['actual_product_name']) ? $_POST['actual_product_name'] : '';
    $confirm_product = isset($_POST['confirm_product']) ? $_POST['confirm_product'] : '';

    // Verify product name matches before deletion
    if ($actual_product_name === $confirm_product) {
        // Start transaction
        $conn->begin_transaction();

        try {
            // Step 1: Delete related data from add_product
            $deleteProductQuery = "DELETE FROM add_product WHERE add_master_id_master = ?";
            $deleteProductStmt = $conn->prepare($deleteProductQuery);
            $deleteProductStmt->bind_param("i", $id_master);
            $deleteProductStmt->execute();

            // Step 2: Delete the main data from add_master
            $deleteMasterQuery = "DELETE FROM add_master WHERE id_master = ? AND product = ?";
            $deleteMasterStmt = $conn->prepare($deleteMasterQuery);
            $deleteMasterStmt->bind_param("is", $id_master, $actual_product_name);
            $deleteMasterStmt->execute();

            // Step 3: Update log_add_product untuk mencatat operator yang menghapus data
            $update_log_query = "
                UPDATE log_add_product 
                SET operator = ?
                WHERE Change_Date IN (
                    SELECT * FROM (
                        SELECT MAX(Change_Date) 
                        FROM log_add_product 
                        WHERE Product = ? 
                        GROUP BY Product
                    ) AS subquery
                )
            ";
            $updateLogStmt = $conn->prepare($update_log_query);
            $updateLogStmt->bind_param("ss", $username, $actual_product_name);
            $updateLogStmt->execute();

            $conn->commit();
            header("Location: ../product/product.php?delete_success=1");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            header("Location: ../product/product.php?delete_error=1");
            exit();
        }

        $deleteProductStmt->close();
        $deleteMasterStmt->close();
        $updateLogStmt->close();
    } else {
        header("Location: ../product/product.php?status=error&message=Product name mismatch");
        exit();
    }
}

$conn->close();
?>

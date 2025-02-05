<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "db_scanbl3";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_account = isset($_POST['id_account']) ? (int)$_POST['id_account'] : 0;
    $actual_username = isset($_POST['actual_username']) ? $_POST['actual_username'] : '';
    $confirm_username = isset($_POST['confirm_username']) ? $_POST['confirm_username'] : '';

    // Verify username matches before deletion
    if ($actual_username === $confirm_username) {
        // Start transaction
        $conn->begin_transaction();

        try {
            // Delete the account
            $delete_query = "DELETE FROM accounts WHERE id_account = ? AND username = ?";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->bind_param("is", $id_account, $actual_username);
            $delete_stmt->execute();

            $conn->commit();
            header("Location: account-management.php?delete_success=1");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            header("Location: account-management.php?delete_error=1");
            exit();
        }

        $delete_stmt->close();
    } else {
        header("Location: account-management.php?delete_error=1=error&message=Username mismatch");
    }
}

$conn->close();
?>
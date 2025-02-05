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

    if ($id_account) {
        // Hash password baru (12345678)
        $new_password = password_hash("12345678", PASSWORD_DEFAULT);

        // Update password di database
        $update_query = "UPDATE accounts SET password = '$new_password' WHERE id_account = $id_account";
        if ($conn->query($update_query)) {
            header("Location: account-management.php?reset_success=1");
            exit();
        } else {
            header("Location: account-management.php?reset_error=1");
            exit();
        }
    } else {
        header("Location: account-management.php?reset_error=1&message=Invalid account ID");
        exit();
    }
}

$conn->close();
?>
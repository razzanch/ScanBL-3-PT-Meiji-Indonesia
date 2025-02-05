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

// Get data from form
$id_account = isset($_POST['id_account']) ? (int)$_POST['id_account'] : 0;
$real_name = isset($_POST['real_name']) ? $conn->real_escape_string($_POST['real_name']) : '';
$username = isset($_POST['username']) ? $conn->real_escape_string($_POST['username']) : '';

// Validate
if (!$id_account || !$real_name || !$username) {
    die("Invalid data.");
}

// Check if username already exists in other accounts
$check_query = "
    SELECT id_account 
    FROM accounts 
    WHERE username = '$username' 
    AND id_account != $id_account
";

$result = $conn->query($check_query);

if ($result->num_rows > 0) {
    // If username already exists, send error message
    header("Location: account-management.php?update_error=1&message=Username already exists");
    exit();
} else {
    // Update data in accounts
    $update_query = "
        UPDATE accounts 
        SET real_name = '$real_name', 
            username = '$username' 
        WHERE id_account = $id_account
    ";

    if ($conn->query($update_query)) {
        header("Location: account-management.php?update_success=1");
        exit();
    } else {
        header("Location: account-management.php?update_error=1&message=Failed to update account");
        exit();
    }
}

$conn->close();
?>
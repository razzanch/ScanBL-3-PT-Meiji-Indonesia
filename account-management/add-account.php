<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_scanbl3";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header("Location: account-management.php?add_error=1");
    exit;
}

// Get form data
$real_name = $_POST['name'];
$username = $_POST['username'];
$plain_password = $_POST['password'];
$role = $_POST['role'];
$date = $_POST['date-account'];

// Check if username already exists
$check_sql = "SELECT username FROM accounts WHERE username = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $username);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    // Username already exists
    header("Location: account-management.php?add_error=1");
    $check_stmt->close();
    $conn->close();
    exit;
}
$check_stmt->close();

// If username doesn't exist, proceed with insert
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

// Query to save data to accounts table
$sql = "INSERT INTO accounts (real_name, username, password, role, date) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $real_name, $username, $hashed_password,$role, $date);

if ($stmt->execute()) {
    header("Location: account-management.php?add_success=1");
} else {
    header("Location: account-management.php?add_error=1");
}

$stmt->close();
$conn->close();
?>
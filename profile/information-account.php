<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

$host = "localhost";
$username = "root";
$password = "";
$database = "db_scanbl3";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM accounts WHERE id_account = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="information-account.css">
    <title>Profile Page</title>
    <script src="../services/chart/chart.umd.js"></script>
</head>
<body>
    <!--SIDE NAVBAR-->
    <div class="container">

        <!--SIDE NAVBAR START-->
        <div class="side-navbar">
            <div class="logo">
                <a href="../landing/landing.php">
                    <img src="../assets/meijiUNMASK.png" alt="Logo">
                </a>
            </div>
            <ul class="menu">

                <a href="../dashboard/dashboard.php">
                    <li>
                        
                            <img src="../assets/dashboard.png" alt="Dashboard Icon">
                            <span>Dashboard</span>
                    </li>
                    </a>

            <a href="../overview/overview.php">
                <li >
                    
                        <img src="../assets/overview.png" alt="Overview Icon">
                        <span>Overview</span>
                </li>
                </a>

                <a href="../add-master/add-master.php">
                <li>
                    
                        <img src="../assets/addmaster.png" alt="Add Master Icon">
                        <span>Add Master</span>
                </li>
                </a>

                <a href="../add/add.php">
                <li>
                    
                        <img src="../assets/add.png" alt="Add Icon">
                        <span>Add</span>
                </li>
                </a>

                <a href="../add-edit/add-edit.php">
                <li>
                    
                        <img src="../assets/add-edit.png" alt="Add Edit Icon">
                        <span>Add Edit</span>
                </li>   
                </a>


                <a href="../preview/preview.php">
                <li>
                    
                        <img src="../assets/preview.png" alt="Preview Icon">
                        <span>Preview</span>
                </li>
                </a>

                <a href="../profile/information-account.php">
                    <li class="active">
                        
                            <img src="../assets/account-information.png" alt="Profile Icon">
                            <span>Profile</span>
                    </li>
                    </a>
            </ul>
        </div>
        <!--SIDE NAVBAR END-->

        <!--MAIN CONTENT & FOOTER-->
        <div class="main-content">

            <!--MAIN CONTENT START-->
           
            <div class="main-header">
                <span class="menu-icon">&#9776;</span>
                <span class="account-icon" onclick="toggleAccountMenu()">
    <img src="../assets/account.png" alt="Account">
    <div id="account-menu" class="account-menu">
        <div class="menu-item">
            <?php echo $_SESSION['username']; ?>
        </div>
        <div class="menu-item" onclick="logout()">
            Logout
        </div>
    </div>
            </div>
            <div class="divider"></div>
            <div class="text-content">
                <div class="text-wrapper">
                    <h1 class="add-master-text">Profile</h1>
                    <p class="data-setup-text">Profile Management</p>    
                </div>
                <span class="history-icon">
                    <img src="../assets/log activity.png" alt="Log Activity">
                </span> 
            </div>


            <div class="form-header">
                <button class="tab-button active">Details</button>
            </div>

            <div class="form-container">
                <form class="barcode-form" id="informationAccountForm">
                <div class="left-form">
        <div class="form-row">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($row['real_name']); ?>" readonly>
        </div>
        <div class="form-row">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" readonly>
        </div>
        <div class="form-row">
            <label for="date-account">Date</label>
            <input type="text" id="date-account" name="date-account" value="<?php echo htmlspecialchars($row['date']); ?>" readonly>
        </div>
    </div>
                    <div class="system-counter-container">
    <div class="form-actions">
        <button type="button" class="btn-add" onclick='openEditModal(<?php echo json_encode([
        "id" => $row["id_account"],
            "realName" => $row["real_name"],
            "username" => $row["username"]
        ]); ?>)'>Edit</button>
        <button type="button" class="btn-change" onclick='openChangePasswordModal(<?php echo json_encode([
            "id" => $row["id_account"]
        ]); ?>)'>Change Password</button>
    </div>
</div>
                </form>

                <div class="statistics-container">
    <div class="chart-card">
        <h3>Hourly Activity Distribution</h3>
        <div class="chart-wrapper">
            <canvas id="hourlyActivityChart"></canvas>
        </div>
    </div>
    
    <div class="chart-card">
        <h3>Change Type Distribution</h3>
        <div class="chart-wrapper">
            <canvas id="changeTypeChart"></canvas>
        </div>
    </div>
    
    <div class="chart-card full-width"> <!-- Add full-width class here -->
        <h3>Daily Progress</h3>
        <div class="chart-wrapper">
            <canvas id="dailyProgressChart"></canvas>
        </div>
    </div>
</div>

            </div>

        
            <!-- Edit Modal -->
<div id="editModal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h5 class="custom-modal-title">Edit Account</h5>
            <button type="button" class="custom-close" onclick="closeEditModal()">&times;</button>
        </div>
        <form id="editForm" action="edit-profile.php" method="POST">
            <input type="hidden" id="edit_id_account" name="id_account">
            
            <div class="custom-form-group">
                <label for="edit_real_name">Real Name:</label>
                <input type="text" id="edit_real_name" name="real_name" required>
            </div>
            
            <div class="custom-form-group">
                <div class="custom-text-note-container">
                    Note: This action is permanent. Please edit the account details carefully!
                </div>
            </div>
            
            <button type="submit" class="custom-btn">Update</button>
        </form>
    </div>
</div>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h5 class="custom-modal-title">Change Password</h5>
            <button type="button" class="custom-close" onclick="closeChangePasswordModal()">&times;</button>
        </div>
        <form id="changePasswordForm" action="change-password.php" method="POST">
            <input type="hidden" id="change_password_id_account" name="id_account">
            
            <div class="custom-form-group">
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            
            <div class="custom-form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>
                <div class="custom-text-note-container">
                    Note: This action is permanent. Please input your new password carefully!
                </div>
            </div>
            
            <button type="submit" class="custom-btn">Change Password</button>
        </form>
    </div>
</div>
            <!--MAIN CONTENT END-->



            <!--FOOTER START-->
            <footer>
                <div class="footer-container">
                    <div class="footer-section">
                        <h3>Alamat</h3>
                        <p>PT. Meiji Indonesia Jl. Mojoparon No.1, Mojokopek, Latek, Kec. Rembang, Pasuruan, Jawa Timur 67153</p>
                        <p>Phone : (0343) 741102</p>
                    </div>
        
                    <div class="footer-section">
                        <h3>Akses Cepat</h3>
                        <p><a href="https://www.meiji.com/global/" target="_blank">Meiji Holdings Co, Ltd.</a></p>
                        <p><a href="https://www.meiji-seika-pharma.co.jp/" target="_blank">Meiji Seika Pharma Co., Ltd.</a></p>
                    </div>
        
                    <div class="footer-section">
                        <img src="../assets/meijiPutih.png" alt="Meiji Logo White">
                        <p>Sebagai pelopor antibiotik berkualitas sejak pendiriannya pada tahun 1974, PT. Meiji Indonesia sebagai anak perusahaan dari Meiji Seika Pharma Co.,Ltd. yang berpusat di Jepang, adalah perusahaan di bidang farmasi yang memiliki standar kualitas produksi tinggi di Indonesia. PT. Meiji Indonesia telah menjadi yang terdepan selama lebih dari 40 tahun, dan terus berupaya meningkatkan kualitas di masa mendatang.</p>
                    </div>
                </div>
            </footer>
            <!--FOOTER END-->
        </div>
        
    

    <script src="information-account.js"></script>
</body>
</html>

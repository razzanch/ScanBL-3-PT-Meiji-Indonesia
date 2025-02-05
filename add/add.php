<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

$username = $_SESSION['username']; // Ambil username dari session
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="add.css">
    <title>Add Page</title>
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
                <li class="active">
                    
                        <img src="../assets/add.png" alt="Add Icon">
                        <span>Add</span>
                </li>
                </a>


                <a href="../preview/preview.php">
                <li >
                    
                        <img src="../assets/preview.png" alt="Preview Icon">
                        <span>Preview</span>
                </li>
                </a>
                <?php if ($role === 'Admin'): ?>
        <!-- Tampilkan menu Accounts jika username adalah adminmeiji -->
        <a href="../account-management/account-management.php">
            <li>
                <img src="../assets/account-management.png" alt="Accounts Icon">
                <span>Accounts</span>
            </li>
        </a>
    <?php else: ?>
        <!-- Tampilkan menu Profile jika username bukan adminmeiji -->
        <a href="../profile/information-account.php">
            <li>
                <img src="../assets/account-information.png" alt="Profile Icon">
                <span>Profile</span>
            </li>
        </a>
    <?php endif; ?>
            </ul>
        </div>
        <!--SIDE NAVBAR END-->

        <!--MAIN CONTENT & FOOTER-->
        <div class="main-content">

             <!--MAIN CONTENT START-->
           
             <div class="main-header">
                <span class="menu-icon">
                    &#9776;
                </span>
                <span class="account-icon" onclick="toggleAccountMenu()">
    <img src="../assets/account.png" alt="Account">
    <div id="account-menu" class="account-menu">
    <div class="menu-item" onclick="redirectBasedOnRole()">
    <?php echo $_SESSION['username']; ?>
</div>

<script>
function redirectBasedOnRole() {
    <?php if ($_SESSION['role'] === 'Admin'): ?>
        window.location.href = "../account-management/account-management.php";
    <?php else: ?>
        window.location.href = "../profile/information-account.php";
    <?php endif; ?>
}
</script>
        <div class="menu-item" onclick="logout()">
            Logout
        </div>
    </div>
</span>
            </div>
            <div class="divider"></div>
            <div class="text-content">
                <div class="text-wrapper">
                    <h1 class="add-master-text">Add</h1>
                    <p class="data-setup-text">Barcode Scanner</p>    
                </div>
                <span class="history-icon">
                    <img src="../assets/log activity.png" alt="Log Activity">
                </span> 
            </div>

            <div class="form-header">
                <button class="tab-button active">RSS-CODE</button>
            </div>

            <div class="form-container">
                <form class="barcode-form">
                    <!-- Bagian Kiri: Form utama -->
                    <div class="left-form">
                        <div class="form-row">
                            <label for="product">Product</label>
                            <select id="product" name="product">
                                <option value="">Select Product</option>
                            </select>                            
                        </div>
                        <div class="form-row">
                            <label for="rss-code">RSS-Code</label>
                            <input type="text" id="rss-code" name="rss-code" readonly>
                        </div>
                        <div class="form-row">
                            <label for="jam-code">JAM-Code</label>
                            <input type="text" id="jam-code" name="jam-code" readonly>
                        </div>
                        <div class="form-row">
                            <label for="lot-number">No. Lot</label>
                            <input type="text" id="lot-number" name="lot-number" placeholder="Press 'Enter' to Confirm Lot Number Input">
                        </div>
                        <div class="form-row">
                            <label for="date">Date</label>
                            <input type="text" id="date" name="date" readonly>
                        </div>                                                
                        <div class="form-row">
                            <label for="counter">Counter</label>
                            <input type="text" id="counter" name="counter" readonly>
                        </div>
                    </div>
                    
                    <!-- Bagian Kanan: System Counter -->
                    <div class="system-counter-container">
                        <label for="system-counter">System Counter</label>
                        <div class="input-container">
                            <input type="text" id="system-counter" placeholder="Enter system counter">
                            <div class="loading-spinner"></div>
                        </div>
                        <textarea id="system-counter-large" name="system-counter-large" readonly></textarea>
                        <div class="form-actions">
                            <!-- <button type="submit" class="btn-add">Add</button> -->
                            <button type="reset" class="btn-clear">Clear</button>
                        </div>
                    </div>
                </form>
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
        
    

    <script src="add.js"></script>
</body>
</html>

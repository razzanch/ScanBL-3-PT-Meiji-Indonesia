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
    <link rel="stylesheet" href="dashboard.css">
    <script src="../services/chart/chart.umd.js"></script>

    <title>Dashboard Page</title>
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
        <li class="active">
            <img src="../assets/dashboard.png" alt="Dashboard Icon">
            <span>Dashboard</span>
        </li>
    </a>

    <a href="../overview/overview.php">
        <li>
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

    <a href="../preview/preview.php">
        <li>
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
                    <h1 class="add-master-text">Dashboard</h1>
                    <p class="data-setup-text">Data Analytics (General)</p>    
                </div>
                <span class="history-icon">
                    <img src="../assets/log activity.png" alt="Log Activity">
                </span> 
            </div>


<div class="form-header">
    <button id="current-date-button" class="tab-button active"></button>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const button = document.getElementById('current-date-button');

        // Fungsi untuk memformat tanggal
        const formatTanggal = (tanggal) => {
            const bulan = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];
            const hari = tanggal.getDate();
            const bulanNama = bulan[tanggal.getMonth()];
            const tahun = tanggal.getFullYear();
            return `${hari} ${bulanNama} ${tahun}`;
        };

        // Atur teks tombol ke tanggal saat ini
        button.textContent = formatTanggal(new Date());
    });
</script>


<div class="form-container">
    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="card">
            <h3>Total Scans Today</h3>
            <div id="scans-value" class="card-value">Loading...</div>
        </div>
        <div class="card">
            <h3>Active Lot Numbers</h3>
            <div id="lots-value" class="card-value">Loading...</div>
        </div>
        <div class="card">
            <h3>Active Products</h3>
            <div id="products-value" class="card-value">Loading...</div>
        </div>
    </div>

    <div class="divider-tren"></div>

    <!-- Charts Container -->
    <div class="charts-container">
        <div class="chart-card">
            <h3>Monthly Scan Activity</h3>
            <div class="chart-wrapper">
                <canvas id="monthly-chart"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <h3>Product Distribution</h3>
            <div class="chart-wrapper">
                <canvas id="product-chart"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-card full-width">
    <h3>Database Operations Trend</h3>
    <div class="chart-wrapper">
        <canvas id="log-trend-chart"></canvas>
    </div>
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
    </div>

    <script src="dashboard.js"></script>
</body>
</html>

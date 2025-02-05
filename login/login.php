<?php
session_start();

// Jika sudah login, langsung arahkan ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard/dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT. Meiji Indonesia</title>
    <link rel="stylesheet" href="login.css">
    <title>Login Page</title>
</head>
<body>



    <!--HEADER START-->
    <header>
        <div class="logo-container">
            <img src="../assets/meijiMerah.png" alt="Meiji Logo">
            <span>PT. Meiji Indonesia</span>
        </div>
        <div class="logo-container">
            <img src="../assets/ScanBL3.png" alt="Meiji Logo">
        </div>
    </header>

    <!--HEADER END-->




    <!-- MAIN CONTENT START -->
    <main>
        <div class="main-content">
            <div class="image-container">
                <img src="../assets/BLMeiji.png" alt="BLMeiji">
            </div>
            <div class="overlay-container">
                <div class="login-container">
                    <div class="login-form">
                        <h1>SIGN IN
                        <img src="../assets/back.png" alt="Back" class="back-icon" onclick="window.location.href='../landing/landing.php'">
                        </h1>
                        <form>
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" id="username" placeholder="Username" required>
                            </div>
                            <div class="form-group">
                            <div class="password-container">
                            <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter Password">
                    <img src="../assets/show-pw.png" alt="Show Password" class="password-toggle" id="passwordToggle">
                </div>
                            </div>
                            <button type="submit" class="signin-button">Sign in</button>
                        </form>
                    </div>
                    <div class="welcome-message">
                        <h2>Sign in to get started and streamline your product management with barcode scanning!</h2>
                    </div>
                </div>        
            </div>
        </div>
    </main>
    
<!-- MAIN CONTENT END -->





    <!--FOOTER START-->
    <footer>
        <div class="footer-container">
            <!-- Kolom Kiri -->
            <div class="footer-section">
                <h3>Alamat</h3>
                <p>PT. Meiji Indonesia Jl. Mojoparon No.1, Mojokopek, Latek, Kec. Rembang, Pasuruan, Jawa Timur 67153</p>
                <p>Phone : (0343) 741102</p>
            </div>
    
            <!-- Kolom Tengah -->
            <div class="footer-section">
                <h3>Akses Cepat</h3>
                <p><a href="https://www.meiji.com/global/" target="_blank">Meiji Holdings Co, Ltd.</a></p>
                <p><a href="https://www.meiji-seika-pharma.co.jp/" target="_blank">Meiji Seika Pharma Co., Ltd.</a></p>
            </div>
    
            <!-- Kolom Kanan -->
            <div class="footer-section">
                <img src="../assets/meijiPutih.png" alt="Meiji Logo White">
                <p>Sebagai pelopor antibiotik berkualitas sejak pendiriannya pada tahun 1974, PT. Meiji Indonesia sebagai anak perusahaan dari Meiji Seika Pharma Co.,Ltd. yang berpusat di Jepang, adalah perusahaan di bidang farmasi yang memiliki standar kualitas produksi tinggi di Indonesia. PT. Meiji Indonesia telah menjadi yang terdepan selama lebih dari 40 tahun, dan terus berupaya meningkatkan kualitas di masa mendatang.</p>
            </div>
        </div>
    </footer>
    <!--FOOTER END-->



   <script src="login.js"></script>

</body>
</html>
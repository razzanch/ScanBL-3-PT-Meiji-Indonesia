document.addEventListener('DOMContentLoaded', function() {
    const menuIcon = document.querySelector('.menu-icon');
    const sideNavbar = document.querySelector('.side-navbar');
    const mainContent = document.querySelector('.main-content');
    const logoImg = document.querySelector('.side-navbar .logo img');
    const menuItems = document.querySelectorAll('.side-navbar .menu li');

    let isNavbarCollapsed = false;
    let isOriginalLogo = true;

    const originalLogoSrc = '../assets/meijiUNMASK.png';
    const alternateLogoSrc = '../assets/circleMeiji.png';

    // Tambahkan event listener untuk menuIcon
    menuIcon.addEventListener('click', toggleNavbar);

    window.addEventListener('resize', function() {
        if (window.innerWidth < 768) {
            if (!isNavbarCollapsed) { 
                toggleNavbar();
            }
        } else {
            toggleNavbar();
        }
    });

    function toggleNavbar() {
        sideNavbar.style.width = isNavbarCollapsed ? '200px' : '70px';
        mainContent.style.marginLeft = isNavbarCollapsed ? '200px' : '70px';

        if (isOriginalLogo) {
            logoImg.src = alternateLogoSrc;
            logoImg.style.width = '50px';
            logoImg.style.height = '50px';
        } else {
            logoImg.src = originalLogoSrc;
            logoImg.style.width = '100%';
            logoImg.style.height = '100%';
        }

        menuItems.forEach(item => {
            const span = item.querySelector('span');
            span.style.display = isNavbarCollapsed ? 'inline-block' : 'none';
        });

        isNavbarCollapsed = !isNavbarCollapsed;
        isOriginalLogo = !isOriginalLogo;
    }

    // Navbar hover effects
    sideNavbar.addEventListener('mouseenter', function() {
        if (isNavbarCollapsed) {
            sideNavbar.style.width = '200px';
            mainContent.style.marginLeft = '200px';
            logoImg.src = originalLogoSrc;
            logoImg.style.width = '100%';
            logoImg.style.height = '100%';

            menuItems.forEach(item => {
                const span = item.querySelector('span');
                span.style.display = 'inline-block';
            });
        }
    });

    sideNavbar.addEventListener('mouseleave', function() {
        if (isNavbarCollapsed) {
            sideNavbar.style.width = '70px';
            mainContent.style.marginLeft = '70px';
            logoImg.src = alternateLogoSrc;
            logoImg.style.width = '50px';
            logoImg.style.height = '50px';

            menuItems.forEach(item => {
                const span = item.querySelector('span');
                span.style.display = 'none';
            });
        }
    });
});


document.addEventListener('DOMContentLoaded', function() {
    const productDropdown = document.getElementById('product');
    const lotDropdown = document.getElementById('nolot');
 
    // Disable lot dropdown initially
    lotDropdown.disabled = false;
 
    // Populate Product Dropdown
    async function fetchProducts() {
        try {
            const response = await fetch('previewget.php?action=get_products');
            const result = await response.json();
 
            if (result.status !== 'success') {
                throw new Error(result.message || 'Failed to fetch products');
            }
 
            result.data.forEach(product => {
                const option = document.createElement('option');
                option.value = product.id_master;
                option.textContent = product.product;
                productDropdown.appendChild(option);
            });
        } catch (error) {
            console.error('Error:', error);
            showNotification('Unable to load products. Please try again.', false);
        }
    }
 
    // Populate Lot Dropdown
    async function fetchLots(selectedProduct) {
        try {
            lotDropdown.innerHTML = '<option value="">Select No. Lot</option>';
 
            if (!selectedProduct) return;
 
            const response = await fetch(`previewget.php?action=get_lots&product=${selectedProduct}`);
            const result = await response.json();
 
            if (result.status !== 'success') {
                throw new Error(result.message || 'Failed to fetch lot numbers');
            }
 
            result.data.forEach(lot => {
                const option = document.createElement('option');
                option.value = lot;
                option.textContent = lot;
                lotDropdown.appendChild(option);
            });
        } catch (error) {
            console.error('Error:', error);
            showNotification('Unable to load lot numbers. Please try again.', false);
        }
    }
 
    // Initial product fetch
    fetchProducts();
 
    // Event listener for product dropdown
    productDropdown.addEventListener('change', () => {
        const selectedProduct = productDropdown.value;
 
        if (selectedProduct === '') {
            // Disable lot dropdown and reset
            lotDropdown.disabled = true;
            lotDropdown.innerHTML = '<option value="">Select No. Lot</option>';
        } else {
            // Enable lot dropdown and fetch lots
            lotDropdown.disabled = false;
            fetchLots(selectedProduct);
        }
    });
 
    // Event listener for lot dropdown
    lotDropdown.addEventListener('click', () => {
        if (productDropdown.value === '') {
            showNotification('Please select a Product first.', false);
            lotDropdown.blur();
        }
    });
 
    // Event listener for form submission
    document.querySelector('.barcode-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const productDropdown = document.getElementById('product');
        const lotDropdown = document.getElementById('nolot');
        console.log('Selected Product:', productDropdown.value);
        console.log('Selected Lot:', lotDropdown.value);
        if (productDropdown.value === '' || lotDropdown.value === '') {
            showNotification('Please select both Product and Lot Number.', false);
            return;
        }
        try {
            const response = await fetch('previewcheck.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product=${productDropdown.value}&nolot=${lotDropdown.value}`
            });
            const result = await response.json();
            console.log(result);
            if (result.status === 'success') {
                window.location.href = '../report/report.php';
            } else {
                showNotification('Data not found', false);
                productDropdown.value = '';
                lotDropdown.value = '';
                lotDropdown.disabled = true;
                lotDropdown.innerHTML = '<option value="">Select No. Lot</option>';
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again.', false);
        }
    });
    // Fungsi untuk menampilkan notifikasi
    function showNotification(message, isSuccess) {
        // Buat elemen notifikasi
        const notification = document.createElement('div');
        notification.innerHTML = `
<div style="display: flex; align-items: center; gap: 10px;">
<img src="../assets/${isSuccess ? 'icon-success.png' : 'icon-error.png'}" alt="${isSuccess ? 'Success' : 'Error'}" style="width: 24px; height: 24px;">
<span>${message}</span>
</div>
        `;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: ${isSuccess ? '#4CAF50' : '#F44336'};
            color: white;
            padding: 15px;
            border-radius: 4px;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            min-width: 300px;
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            display: flex;
            align-items: center;
            animation: slideIn 0.5s ease-out;
        `;
 
        // Tambahkan notifikasi ke body
        document.body.appendChild(notification);
 
        // Hapus notifikasi setelah 2 detik
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.5s ease-out';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 500); // Waktu untuk animasi slideOut
        }, 2000); // Notifikasi muncul selama 2 detik
    }
 
    // Animasi CSS untuk notifikasi
    const style = document.createElement('style');
    style.innerHTML = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
});
 
document.addEventListener('DOMContentLoaded', function () {
    const historyIcon = document.querySelector('.history-icon');
 
    historyIcon.addEventListener('click', function () {
        console.log('History icon clicked');
        // Arahkan ke halaman log_activity.php
        window.location.href = '../log_activity/log_activity.php?from=preview';
    });
});
 
// Fungsi untuk menampilkan/menyembunyikan menu
function toggleAccountMenu() {
    const menu = document.getElementById('account-menu');
    menu.classList.toggle('show');
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const isClickInside = menu.contains(event.target) || 
                            event.target.closest('.account-icon');
        if (!isClickInside && menu.classList.contains('show')) {
            menu.classList.remove('show');
        }
    });
}
 
// Fungsi untuk logout
function logout() {
    fetch('../login/logout.php') // Buat file logout.php untuk menghapus session
        .then(response => {
            if (response.ok) {
                window.location.href = '../login/login.php'; // Redirect ke halaman login
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}
 
// Tutup menu saat mengklik di luar menu
document.addEventListener('click', function (event) {
    const accountMenu = document.getElementById('account-menu');
    const accountIcon = document.querySelector('.account-icon');
 
    // Jika yang diklik bukan bagian dari account-icon atau account-menu, sembunyikan menu
    if (!accountIcon.contains(event.target) && !accountMenu.contains(event.target)) {
        accountMenu.classList.remove('show');
    }
});
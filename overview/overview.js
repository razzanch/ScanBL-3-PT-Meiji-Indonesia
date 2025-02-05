document.addEventListener('DOMContentLoaded', function () {
    const menuIcon = document.querySelector('.menu-icon');
    const sideNavbar = document.querySelector('.side-navbar');
    const mainContent = document.querySelector('.main-content');
    const logoImg = document.querySelector('.side-navbar .logo img');
    const menuItems = document.querySelectorAll('.side-navbar .menu li');
    const searchField = document.querySelector('.overview-search');
    const tableBody = document.querySelector('table tbody');
    const paginationContainer = document.querySelector('.pagination');

    let isNavbarCollapsed = false;
    let isOriginalLogo = true;
    let isBarcodeScanMode = false;

    const originalLogoSrc = '../assets/meijiUNMASK.png';
    const alternateLogoSrc = '../assets/circleMeiji.png';

    // Fokus ke search field saat halaman pertama kali dibuka
    searchField.focus();

    // Navbar toggle functionality
    menuIcon.addEventListener('click', toggleNavbar);

    window.addEventListener('resize', function () {
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
    sideNavbar.addEventListener('mouseenter', function () {
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

    sideNavbar.addEventListener('mouseleave', function () {
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

    // Restore search state
    function restoreSearchState() {
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get('search');
        
        if (searchParam) {
            searchField.value = decodeURIComponent(searchParam);
            updateSearchFieldIcon();
        }
    }

    // Search field icon update
    function updateSearchFieldIcon() {
        if (searchField.value.trim() === '') {
            searchField.classList.remove('has-text');
        } else {
            searchField.classList.add('has-text');
        }
    }

    // Barcode icon toggle functionality
    searchField.addEventListener('click', function (e) {
        const rect = searchField.getBoundingClientRect();
        const clickX = e.clientX - rect.left;

        // Check if barcode icon is clicked (left side)
        if (clickX <= 40) {
            
        }
        // Check if clear icon is clicked (right side)
        else if (clickX >= rect.width - 40 && searchField.classList.contains('has-text')) {
            searchField.value = '';
            updateSearchFieldIcon();
            // Tampilkan semua data
            window.location.href = 'overview.php';
        }
    });

    // Event listener untuk pencarian dengan tombol Enter
    searchField.addEventListener('keydown', function (event) {
        const searchValue = searchField.value.trim();

        // Deteksi tombol Enter
        if (event.key === 'Enter' && !isBarcodeScanMode) {
            event.preventDefault(); // Mencegah aksi default
            if (searchValue !== '') {
                // Redirect dengan parameter pencarian
                window.location.href = `overview.php?search=${encodeURIComponent(searchValue)}`;
            }
        }
    });

    // Event listener untuk perubahan input
    searchField.addEventListener('input', function () {
        const searchValue = searchField.value.trim();

        if (searchValue === '' && !isBarcodeScanMode) {
            // Jika input kosong, tampilkan semua data
            window.location.href = 'overview.php';
        }
    });

    // Delete functionality
    tableBody.addEventListener('click', function (e) {
        const deleteBtn = e.target.closest('.delete-btn');
        
        if (deleteBtn) {
            e.preventDefault();
            const productId = deleteBtn.getAttribute('data-id');
            
            // Confirm deletion
            if (confirm('Are you sure you want to delete this record?')) {
                // Redirect to delete endpoint with ID
                window.location.href = `overview.php?delete=${productId}&id=${productId}`;
            }
        }
    });

    // Handle delete success/error messages
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('delete_success')) {
    showNotification('Record deleted successfully', true); // Notifikasi sukses
}
if (urlParams.get('delete_error')) {
    showNotification('Error deleting record', false); // Notifikasi gagal
}




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

    // Initial icon state
    updateSearchFieldIcon();

    // Call restore search state on page load
    restoreSearchState();
});

document.addEventListener('DOMContentLoaded', function () {
    const historyIcon = document.querySelector('.history-icon');

    historyIcon.addEventListener('click', function () {
        // Arahkan ke halaman log_activity.php
        window.location.href = '../log_activity/log_activity.php?from=overview';
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
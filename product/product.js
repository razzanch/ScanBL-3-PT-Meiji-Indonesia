document.addEventListener('DOMContentLoaded', function() {
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

    // Create notification container if it doesn't exist
    let notificationContainer = document.createElement('div');
    notificationContainer.id = 'notification-container';
    document.body.appendChild(notificationContainer);

    // Function to show notification
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        const iconSrc = type === 'success' ? '../assets/icon-success.png' : '../assets/icon-error.png';
        const bgColor = type === 'success' ? '#4CAF50' : '#f44336';

        notification.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px;">
                <img src="${iconSrc}" alt="${type}" style="width: 24px; height: 24px;">
                <span>${message}</span>
            </div>
        `;

        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: ${bgColor};
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
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        `;

        notificationContainer.appendChild(notification);

        // Fade in
        setTimeout(() => {
            notification.style.opacity = '1';
        }, 100);

        // Remove notification after 2 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 2000);
    }

    // Navbar toggle functionality
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
            window.location.href = 'product.php';
        }
        else if (clickX >= rect.width - 40 && !searchField.classList.contains('has-text')) {
            const searchValue = searchField.value.trim();
            function validateInput(input) {
                const forbiddenChars = /[<>\"'&/=]/g;
                return !forbiddenChars.test(input);
            }
                        event.preventDefault(); // Mencegah aksi default
        
                if (!validateInput(searchValue)) {
                    showNotification("Search query contains prohibited characters! (<, >, &, \", ', /, =)", false);
                    searchField.value = ""; // Kosongkan input jika mengandung karakter terlarang
                    return;
                }
            if (searchValue !== '') {
                // Redirect dengan parameter pencarian
                window.location.href = `product.php?search=${encodeURIComponent(searchValue)}`;
            }
        }
    });

    

    // Event listener untuk pencarian dengan tombol Enter
    searchField.addEventListener('keydown', function (event) {
        const searchValue = searchField.value.trim();

        function validateInput(input) {
            const forbiddenChars = /[<>\"'&/=]/g;
            return !forbiddenChars.test(input);
        }
    
        // Deteksi tombol Enter
        if (event.key === "Enter" && !isBarcodeScanMode) {
            event.preventDefault(); // Mencegah aksi default
    
            if (!validateInput(searchValue)) {
                showNotification("Search query contains prohibited characters! (<, >, &, \", ', /, =)", false);
                searchField.value = ""; // Kosongkan input jika mengandung karakter terlarang
                return;
            }
            if (searchValue !== '') {
                // Redirect dengan parameter pencarian
                window.location.href = `product.php?search=${encodeURIComponent(searchValue)}`;
            }
        }
    });

    // Event listener untuk perubahan input
    searchField.addEventListener('input', function () {
        const searchValue = searchField.value.trim();

        if (searchValue === '' && !isBarcodeScanMode) {
            // Jika input kosong, tampilkan semua data
            window.location.href = 'product.php';
        }
    });

    // Delete functionality
    tableBody.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.delete-btn');
        
        if (deleteBtn) {
            e.preventDefault();
            const productId = deleteBtn.getAttribute('data-id');
            
            // Confirm deletion
            if (confirm('Are you sure you want to delete this record?')) {
                window.location.href = `product.php?delete=${productId}&id=${productId}`;
            }
        }
    });

    // Check URL parameters for notifications
    const urlParams = new URLSearchParams(window.location.search);
    
    // Handle update notifications
    if (urlParams.has('update_success')) {
        showNotification('Data successfully updated');
    } else if (urlParams.has('update_error')) {
        showNotification('Barcodes/RSS-codes are already used by other products', 'error');
    }

    // Handle delete notifications
    if (urlParams.has('delete_success')) {
        showNotification('Data successfully deleted');
    } else if (urlParams.has('delete_error')) {
        showNotification('Error deleting data', 'error');
    }

    // Remove the success/error parameters from URL without refreshing
    if (urlParams.has('update_success') || urlParams.has('update_error') || 
        urlParams.has('delete_success') || urlParams.has('delete_error')) {
        const newUrl = window.location.pathname;
        window.history.replaceState({}, '', newUrl);
    }

    if (tableBody) {
        let rows = tableBody.getElementsByTagName("tr");
    
    if(urlParams.get('search')){
        // Jika hanya ada satu baris dan mengandung teks "Data tidak ditemukan"
        if (rows.length === 1 && rows[0].textContent.includes("Data tidak ditemukan")) {
            showNotification("Data not found", 'error');
        } else if (rows.length > 0) {
            showNotification("Data found");
        }
    }
}

    // Initial icon state
    updateSearchFieldIcon();

    // Call restore search state on page load
    restoreSearchState();
});

// History icon functionality
document.addEventListener('DOMContentLoaded', function () {
    const historyIcon = document.querySelector('.history-icon');

    historyIcon.addEventListener('click', function () {
        // Arahkan ke halaman log_activity.php
        window.location.href = '../add-master/add-master.php';
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

document.addEventListener("DOMContentLoaded", function () {
    const searchField = document.querySelector(".overview-search");
    if (searchField) {
        searchField.focus();
    }
});

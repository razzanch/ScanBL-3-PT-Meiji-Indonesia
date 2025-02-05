//Template--Start

document.addEventListener('DOMContentLoaded', function () {
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

    //Template--end
});

document.addEventListener('DOMContentLoaded', function () {
    // Form elements
    const nameInput = document.getElementById("name");
    const usernameInput = document.getElementById("username");
    const passwordInput = document.getElementById("password");
    const roleInput = document.getElementById("role");
    const dateInput = document.getElementById("date-account");
    const addAccountForm = document.getElementById("addAccountForm");
    const passwordToggle = document.getElementById("passwordToggle");
    const searchField = document.querySelector(".overview-search");
    const tableBody = document.querySelector('table tbody');

    // State variables
    let isBarcodeScanMode = false;

    // Create notification container and styles
    const notificationContainer = document.createElement('div');
    notificationContainer.id = 'notificationContainer';
    document.body.appendChild(notificationContainer);

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


    // Notification function
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
            animation: slideIn 0.5s ease-out;
        `;

        notificationContainer.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.5s ease-out';
            setTimeout(() => {
                notification.remove();
            }, 500);
        }, 2000);
    }

    // Search functions
    function restoreSearchState() {
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get('search');
        
        if (searchParam) {
            searchField.value = decodeURIComponent(searchParam);
            updateSearchFieldIcon();
        }
    }

    function updateSearchFieldIcon() {
        if (searchField.value.trim() === '') {
            searchField.classList.remove('has-text');
            searchField.style.backgroundImage = "url('../assets/account.png'), url('../assets/blackpreview.png')";
        } else {
            searchField.classList.add('has-text');
            searchField.style.backgroundImage = "url('../assets/account.png'), url('../assets/X.png')";
        }
    }


    // Search field events
    if (searchField) {
        searchField.addEventListener('click', function(e) {
            const rect = searchField.getBoundingClientRect();
            const clickX = e.clientX - rect.left;

            if (clickX <= 40) {
               
            } else if (clickX >= rect.width - 40 && searchField.classList.contains('has-text')) {
                searchField.value = '';
                updateSearchFieldIcon();
                localStorage.setItem('shouldScrollToTable', 'true');
                window.location.href = 'account-management.php';
            }
        });

        searchField.addEventListener('keydown', function(event) {
            const searchValue = searchField.value.trim();

            if (event.key === 'Enter' && !isBarcodeScanMode) {
                event.preventDefault();
                if (searchValue !== '') {
                    localStorage.setItem('shouldScrollToTable', 'true');
                    window.location.href = `account-management.php?search=${encodeURIComponent(searchValue)}`;
                }
            }
        });

        searchField.addEventListener('input', function() {
            const searchValue = searchField.value.trim();
            updateSearchFieldIcon();

            if (searchValue === '' && !isBarcodeScanMode) {
                window.location.href = 'account-management.php';
            }
        });
    }


    function scrollToTable() {
        const tableElement = document.querySelector('table');
        if (tableElement) {
            tableElement.scrollIntoView({ behavior: 'smooth' });
        }
    }

    // Form events
    if (addAccountForm) {
        addAccountForm.addEventListener("submit", function(event) {
            event.preventDefault();
            if (!nameInput.value || !usernameInput.value || !passwordInput.value || !roleInput.value) {
                showNotification("Please fill in all fields.", "error");
                return;
            }
    
            const formData = new FormData(addAccountForm);
            
            fetch("add-account.php", {
                method: "POST",
                body: formData
            })
            .then(response => {
                if (response.redirected) {
                    localStorage.setItem('shouldScrollToTable', 'true');
                    window.location.href = response.url;
                }
            })
            .catch(error => {
                console.error("Error:", error);
                showNotification("An error occurred while submitting the form.", "error");
            });
        });
    }

    if (localStorage.getItem('shouldScrollToTable') === 'true') {
        localStorage.removeItem('shouldScrollToTable');
        setTimeout(scrollToTable, 100); // Small delay to ensure table is rendered
    }

    // Password toggle event
    if (passwordToggle) {
        passwordToggle.addEventListener("click", function() {
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordToggle.src = "../assets/hide-pw.png";
            } else {
                passwordInput.type = "password";
                passwordToggle.src = "../assets/show-pw.png";
            }
        });
    }

    const urlParams = new URLSearchParams(window.location.search);

// Handle update notifications

if (urlParams.has('add_success')) {
    showNotification('Account successfully added');
} else if (urlParams.has('update_error')) {
    showNotification('Username is already used by other Account', 'error');
}

if (urlParams.has('update_success')) {
    showNotification('Data successfully updated');
} else if (urlParams.has('update_error')) {
    showNotification('Username is already used by other Account', 'error');
}

// Handle delete notifications
if (urlParams.has('delete_success')) {
    showNotification('Data successfully deleted');
} else if (urlParams.has('delete_error')) {
    showNotification('Username mismatch', 'error');
}

// Handle reset password notifications
if (urlParams.has('reset_success')) {
    showNotification('Password successfully reset to "12345678".');
} else if (urlParams.has('reset_error')) {
    showNotification('Failed to reset password.', 'error');
}

// Remove the success/error parameters from URL without refreshing
if (
    urlParams.has('add_success') ||
    urlParams.has('add_error') ||
    urlParams.has('update_success') ||
    urlParams.has('update_error') ||
    urlParams.has('delete_success') ||
    urlParams.has('delete_error') ||
    urlParams.has('reset_success') ||
    urlParams.has('reset_error')
) {
    const newUrl = window.location.pathname;
    window.history.replaceState({}, '', newUrl);
}

    // Initialize
    if (searchField) {
        updateSearchFieldIcon();
        restoreSearchState();
    }

    // Set current date when typing in name, username, or password fields
    function setCurrentDateTime() {
        if (!dateInput.value) {
            const now = new Date();
    
            // Format YYYY-MM-DD HH:MM:SS
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0'); // Bulan (1-12)
            const day = String(now.getDate()).padStart(2, '0'); // Hari (01-31)
            const hours = String(now.getHours()).padStart(2, '0'); // Jam (00-23)
            const minutes = String(now.getMinutes()).padStart(2, '0'); // Menit (00-59)
            const seconds = String(now.getSeconds()).padStart(2, '0'); // Detik (00-59)
    
            const timestamp = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
            dateInput.value = timestamp;
        }
    }
    
    [nameInput, usernameInput, passwordInput].forEach(input => {
        input.addEventListener("input", setCurrentDateTime);
    });
    
});

document.addEventListener('DOMContentLoaded', function () {
    const historyIcon = document.querySelector('.history-icon');

    historyIcon.addEventListener('click', function () {
        // Arahkan ke halaman log_activity.php
        window.location.href = '../log_activity/log_activity.php?from=account-management';
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
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

    // Add this line to fetch user data when page loads
    fetchUserData();

    //Template--end
});

// Tombol log activity
document.addEventListener('DOMContentLoaded', function () {
    const historyIcon = document.querySelector('.history-icon');

    historyIcon.addEventListener('click', function () {
        // Arahkan ke halaman log_activity.php
        window.location.href = '../log_activity/log_activity.php?from=information-account';
    });
});

// Function to fetch and display user data
function fetchUserData() {
    fetch('get-data-account.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                // Populate the form fields with the received data
                document.getElementById('name').value = data.data.real_name;
                document.getElementById('username').value = data.data.username;
                document.getElementById('date-account').value = data.data.date;
            } 
        });
}

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



//MODAL FOR EDIT & CHANGE PW

function openEditModal(data) {
    const modal = document.getElementById('editModal');
    document.getElementById('edit_id_account').value = data.id;
    document.getElementById('edit_real_name').value = data.realName;
    modal.style.display = "block";
}

function openChangePasswordModal(data) {
    const modal = document.getElementById('changePasswordModal');
    document.getElementById('change_password_id_account').value = data.id;
    modal.style.display = "block";
}

function closeEditModal() {
    document.getElementById('editModal').style.display = "none";
}

function closeChangePasswordModal() {
    document.getElementById('changePasswordModal').style.display = "none";
}

// Combined modal close handler
window.onclick = function(event) {
    var editModal = document.getElementById('editModal');
    var changePasswordModal = document.getElementById('changePasswordModal');
    if (event.target == editModal) {
        closeEditModal();
    }
    if (event.target == changePasswordModal) {
        closeChangePasswordModal();
    }
}

let notificationContainer = document.createElement('div');
    notificationContainer.id = 'notification-container';
    document.body.appendChild(notificationContainer);

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

 // Check URL parameters for notifications
 const urlParams = new URLSearchParams(window.location.search);
    
 // Handle update notifications
 if (urlParams.has('editprofile_success')) {
     showNotification('Profile successfully updated');
 } else if (urlParams.has('editprofile_error')) {
     showNotification('Username is already used by other Account', 'error');
 }

 // Handle delete notifications
 if (urlParams.has('changepw_success')) {
     showNotification('Password successfully changed');
 } else if (urlParams.has('changepw_error')) {
     showNotification('Current password mismatch', 'error');
 }

 // Remove the success/error parameters from URL without refreshing
 if (urlParams.has('editprofile_success') || urlParams.has('editprofile_error') || 
     urlParams.has('changepw_success') || urlParams.has('changepw_error')) {
     const newUrl = window.location.pathname;
     window.history.replaceState({}, '', newUrl);
 }



 //CHART BUSINESS

 document.addEventListener('DOMContentLoaded', function() {
    const username = document.getElementById('username').value;
    fetchAndRenderCharts(username);
});

function fetchAndRenderCharts(username) {
    fetch(`fetch-statistics-profile.php?username=${encodeURIComponent(username)}`)
        .then(response => response.json())
        .then(data => {
            renderHourlyActivityChart(data.hourlyActivity);
            renderChangeTypeChart(data.changeTypeDistribution);
            renderDailyProgressChart(data.dailyProgress);
        })
        .catch(error => console.error('Error fetching statistics:', error));
}

function renderHourlyActivityChart(data) {
    const ctx = document.getElementById('hourlyActivityChart').getContext('2d');
    
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(54, 162, 235, 0.8)');  // #36a2eb
    gradient.addColorStop(1, 'rgba(54, 162, 235, 0.1)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(item => `${item.hour}:00`),
            datasets: [{
                label: 'Activity Count',
                data: data.map(item => item.count),
                backgroundColor: gradient,
                borderColor: '#36a2eb',
                borderWidth: 2,
                borderRadius: 8,
                hoverBackgroundColor: '#36a2eb',
                barPercentage: 0.7
            }]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false,
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    usePointStyle: true,
                    callbacks: {
                        label: (context) => `Activities: ${context.raw}`
                    }
                }
            },
            animation: {
                duration: 1500,
                easing: 'easeInOutQuart'
            }
        }
    });
}

function renderChangeTypeChart(data) {
    const ctx = document.getElementById('changeTypeChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.map(item => item.type),
            datasets: [{
                data: data.map(item => item.count),
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',  // INSERT - #36a2eb
                    'rgba(255, 205, 86, 0.8)',  // UPDATE - #ffcd56
                    'rgba(255, 99, 132, 0.8)'   // DELETE - #ff6384
                ],
                borderColor: 'white',
                borderWidth: 2,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            cutout: '60%',
            plugins: {
                title: {
                    display: true,
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    callbacks: {
                        label: (context) => {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.raw / total) * 100).toFixed(1);
                            return `${context.label}: ${context.raw} (${percentage}%)`;
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 2000,
                easing: 'easeInOutQuart'
            }
        }
    });
}

function renderDailyProgressChart(data) {
    const ctx = document.getElementById('dailyProgressChart').getContext('2d');
    
    const primaryGradient = ctx.createLinearGradient(0, 0, 0, 400);
    primaryGradient.addColorStop(0, 'rgba(136, 132, 216, 0.4)'); // #8884d8
    primaryGradient.addColorStop(1, 'rgba(136, 132, 216, 0)');

    const secondaryGradient = ctx.createLinearGradient(0, 0, 0, 400);
    secondaryGradient.addColorStop(0, 'rgba(130, 202, 157, 0.4)'); // #82ca9d
    secondaryGradient.addColorStop(1, 'rgba(130, 202, 157, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(item => `${item.hour}:00`),
            datasets: [
                {
                    label: 'Total Products',
                    data: data.map(item => item.totalProducts),
                    borderColor: '#8884d8',
                    backgroundColor: primaryGradient,
                    borderWidth: 3,
                    yAxisID: 'y1',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#8884d8',
                    pointBorderColor: '#8884d8'
                },
                {
                    label: 'Active Lots',
                    data: data.map(item => item.activeLots),
                    borderColor: '#82ca9d',
                    backgroundColor: secondaryGradient,
                    borderWidth: 3,
                    yAxisID: 'y2',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#82ca9d',
                    pointBorderColor: '#82ca9d'
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            },
            scales: {
                y1: {
                    type: 'linear',
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Total Products',
                        font: {
                            weight: 'bold'
                        }
                    },
                    grid: {
                        drawBorder: false,
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                y2: {
                    type: 'linear',
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Active Lots',
                        font: {
                            weight: 'bold'
                        }
                    },
                    grid: {
                        display: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    usePointStyle: true
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart'
            }
        }
    });
}




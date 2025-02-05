document.addEventListener('DOMContentLoaded', function() {
    const menuIcon = document.querySelector('.menu-icon');
    const sideNavbar = document.querySelector('.side-navbar');
    const mainContent = document.querySelector('.main-content');
    const logoImg = document.querySelector('.side-navbar .logo img');
    const menuItems = document.querySelectorAll('.side-navbar .menu li');
    const searchField = document.querySelector('.overview-search');
    const tableBody = document.querySelector('table tbody');

    let isNavbarCollapsed = false;
    let isOriginalLogo = true;
    let isBarcodeScanMode = false;

    const originalLogoSrc = '../assets/meijiUNMASK.png';
    const alternateLogoSrc = '../assets/circleMeiji.png';

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
});

// Add this new function after the existing chart functions
function updateLogTrendChart(data) {
    const ctx = document.getElementById('log-trend-chart');

    if (window.logTrendChart) {
        window.logTrendChart.destroy();
    }

    const dates = [...new Set(data.map(item => item.date))];
    const formattedDates = dates.map(date => new Date(date).toISOString().split('T')[0]);

    const insertData = dates.map(date => data.find(item => item.date === date && item.Change_Type === 'INSERT')?.count || 0);
    const updateData = dates.map(date => data.find(item => item.date === date && item.Change_Type === 'UPDATE')?.count || 0);
    const deleteData = dates.map(date => data.find(item => item.date === date && item.Change_Type === 'DELETE')?.count || 0);

    window.logTrendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: formattedDates,
            datasets: [
                {
                    label: 'Insertions',
                    data: insertData,
                    borderColor: '#36a2eb',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#36a2eb',
                    pointBorderColor: '#36a2eb',
                    pointBorderWidth: 1,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Updates',
                    data: updateData,
                    borderColor: '#ffcd56',
                    backgroundColor: 'rgba(255, 205, 86, 0.1)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#ffcd56',
                    pointBorderColor: '#ffcd56',
                    pointBorderWidth: 1,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Deletions',
                    data: deleteData,
                    borderColor: '#ff6384',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#ff6384',
                    pointBorderColor: '#ff6384',
                    pointBorderWidth: 1,
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                title: {
                    display: true,
                    text: 'Database Operations Trend (Last 30 Days)',
                    padding: {
                        top: 10,
                        bottom: 30
                    },
                    font: {
                        size: 16
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    padding: 10
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Operations',
                        padding: {
                            bottom: 10
                        }
                    },
                    grid: {
                        drawBorder: true,
                        color: 'rgba(0, 0, 0, 0.2)'
                    },
                    ticks: {
                        padding: 8
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date',
                        padding: {
                            top: 10
                        }
                    },
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.2)'
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45,
                        padding: 8,
                        autoSkip: true,
                        maxTicksLimit: 15
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            },
            layout: {
                padding: {
                    left: 10,
                    right: 10,
                    top: 10,
                    bottom: 10
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart'
            }
        }
    });
}


// Modify the existing fetchDashboardData function to include the new chart
function fetchDashboardData() {
    fetch('fetch_dashboard_data.php')
        .then(response => response.json())
        .then(data => {
            // Existing updates...
            document.getElementById('scans-value').textContent = data.total_scans;
            document.getElementById('lots-value').textContent = data.active_lots;
            document.getElementById('products-value').textContent = data.active_products;

            // Update existing charts
            updateMonthlyChart(data.monthly_data);
            updateProductChart(data.product_data);
            
            // Update new log trend chart
            updateLogTrendChart(data.log_trend_data);
        })
        .catch(error => console.error('Error fetching dashboard data:', error));
}


function updateMonthlyChart(data) {
    const ctx = document.getElementById('monthly-chart');

    if (window.monthlyChart) {
        window.monthlyChart.destroy();
    }

    window.monthlyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(item => item.month),
            datasets: [
                {
                    label: 'Total Scans',
                    data: data.map(item => item.scan_count),
                    borderColor: '#8884d8',
                    backgroundColor: '#8884d8',
                    pointBackgroundColor: '#8884d8',
                    pointBorderColor: '#8884d8',
                    pointStyle: 'circle',
                    tension: 0.1,
                    fill: false
                },
                {
                    label: 'Unique Lots',
                    data: data.map(item => item.unique_lots),
                    borderColor: '#82ca9d',
                    backgroundColor: '#82ca9d',
                    pointBackgroundColor: '#82ca9d',
                    pointBorderColor: '#82ca9d',
                    pointStyle: 'circle',
                    tension: 0.1,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart'
            }
        }
    });
}


function updateProductChart(data) {
    const ctx = document.getElementById('product-chart');
    const totalCount = data.reduce((sum, item) => sum + Number(item.scan_count), 0);

    if (window.productChart) {
        window.productChart.destroy();
    }

    const centerTextPlugin = {
        id: 'centerText',
        afterDraw: function(chart) {
            if (chart.config.type === 'doughnut') {
                const ctx = chart.ctx;
                const width = chart.width;
                const height = chart.height;
                ctx.save();
                const centerX = ((chart.chartArea.left + chart.chartArea.right) / 2);
                const centerY = ((chart.chartArea.top + chart.chartArea.bottom) / 2);
                const fontSize = Math.min(width, height) / 8;
                ctx.font = `${fontSize}px Arial`;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = '#000000';
                ctx.fillText(totalCount.toString(), centerX, centerY);
                ctx.restore();
            }
        }
    };

    window.productChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.map(item => item.product),
            datasets: [{
                data: data.map(item => Number(item.scan_count)),
                backgroundColor: ['#0088FE', '#00C49F', '#FFBB28', '#FF8042'],
                borderColor: 'white',
                borderWidth: 2,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
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
            cutout: '80%'
        },
        plugins: [centerTextPlugin],
        animation: {
            animateRotate: true,
            animateScale: true,
            duration: 2000,
            easing: 'easeInOutQuart'
        }
    });
}




// Fetch data when page loads
document.addEventListener('DOMContentLoaded', function() {
    fetchDashboardData();
    // Refresh data every 5 minutes
    setInterval(fetchDashboardData, 300000);
});

document.addEventListener('DOMContentLoaded', function () {
    const historyIcon = document.querySelector('.history-icon');

    historyIcon.addEventListener('click', function () {

        console.log('History icon clicked');
        // Arahkan ke halaman log_activity.php
        window.location.href = '../log_activity/log_activity.php?from=dashboard';
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



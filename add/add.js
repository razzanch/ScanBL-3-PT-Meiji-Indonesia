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

    // Ambil elemen loading
    const loadingElement = document.querySelector('.loading');

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



document.addEventListener('DOMContentLoaded', function () {
    const productSelect = document.getElementById('product');
    const lotNumberInput = document.getElementById('lot-number');
    const rssCodeInput = document.getElementById('rss-code');
    const jamCodeInput = document.getElementById('jam-code');
    const dateInput = document.getElementById('date');
    const systemCounterLarge = document.getElementById('system-counter-large');
    const counterInput = document.getElementById('counter');
    const systemCounterInput = document.getElementById('system-counter');
    const loadingSpinner = document.querySelector('.loading-spinner');

    // Create audio element for success sound
    const successSound = new Audio('../assets/success.mp3');

    let currentCounter = 0; // Menyimpan nilai counter yang ada
    let timeoutId = null; // Untuk menyimpan setTimeout ID
    let notifmm = true;

    // Fungsi untuk mendapatkan waktu saat ini dalam format 'YYYY-MM-DD HH:MM:SS'
    function getCurrentDateTime() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`; // Format dengan spasi
    }

    

    // Ambil nilai terakhir dari counter berdasarkan product dan lot_number
    function fetchLastCounter() {
        const product = productSelect.value;
        const lotNumber = lotNumberInput.value;
        
        if (!product || !lotNumber) return;

        fetch(`get_data.php?product=${encodeURIComponent(product)}&lot_number=${encodeURIComponent(lotNumber)}`)
            .then(response => response.json())
            .then(data => {
                currentCounter = data.last_counter || 0; // Ambil nilai terakhir dari database
                counterInput.value = currentCounter; // Update tampilan counter
            })
            .catch(error => console.error('Error fetching last counter:', error));
    }

    // Fetch daftar produk dari database
    fetch('get_data.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error("Error fetching product list:", data.error);
                return;
            }
            data.forEach(product => {
                const option = document.createElement('option');
                option.value = product;
                option.textContent = product;
                productSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error fetching product list:', error));

    // Event listener saat produk dipilih
    productSelect.addEventListener('change', function () {
        const selectedProduct = this.value;

        // Reset nilai No Lot setiap kali produk berubah
        lotNumberInput.value = '';

        // Reset nilai Counter setiap kali produk berubah
        counterInput.value = '';

        // Reset nilai System Counter setiap kali produk berubah
        systemCounterLarge.value = '';

        if (selectedProduct) {
            fetch(`get_data.php?product=${encodeURIComponent(selectedProduct)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error("Error fetching product details:", data.error);
                        rssCodeInput.value = '';
                        jamCodeInput.value = '';
                        return;
                    }
                    
                    rssCodeInput.value = data.rss_code || '';
                    jamCodeInput.value = data.jam_code || '-';

                    dateInput.value = getCurrentDateTime(); // Set date otomatis setiap kali produk dipilih
                })
                .catch(error => console.error('Error fetching product details:', error));
        } else {
            rssCodeInput.value = '';
            jamCodeInput.value = '';
        }
    });



    // Event listener untuk tombol Enter pada No Lot
    lotNumberInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Mencegah perilaku default (misalnya, submit form)
            systemCounterInput.focus(); // Pindahkan fokus ke System Counter
        }
    });

    // Fetch counter immediately when lot number is being typed
    lotNumberInput.addEventListener('input', function() {
        if (this.value.trim() !== '') {
            fetchLastCounter();
        }
    });

    // Keep the existing change event as backup
    lotNumberInput.addEventListener('change', function() {
        fetchLastCounter();
    });

    // Notifikasi untuk No Lot ketika belum pilih Product
    document.getElementById("lot-number").addEventListener("click", function() {
        let selectedProduct = document.getElementById("product").value;
        
        if (!selectedProduct) {
            showNotification("Please select the product first!", "error");
            lotNumberInput.blur();
        }
    });

    // Notifikasi untuk System Counter ketika belum mengisi No Lot
    document.getElementById("system-counter").addEventListener("click", function() {
        let lotNumberInput = document.getElementById("lot-number").value;
        
        if (!lotNumberInput) {
            showNotification("Please fill in the Lot Number first!", "error");
            systemCounterInput.blur();
        }
    });

    // Function to show notification
    function showNotification(message) {
        const notification = document.createElement('div');
        const iconSrc = '../assets/icon-error.png';
        const bgColor = '#f44336';

        notification.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px;">
                <img src="${iconSrc}" alt="error" style="width: 24px; height: 24px;">
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

        document.body.appendChild(notification);

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

    // Tambahkan elemen untuk animasi loading di dalam input
    loadingSpinner.classList.add('loading-spinner');

    // Event listener untuk System Counter
    systemCounterInput.addEventListener('input', function () {
        const inputValue = systemCounterInput.value;

        if (inputValue === rssCodeInput.value) {
            currentCounter += 1; // Increment counter
            counterInput.value = currentCounter; // Update tampilan counter
            systemCounterLarge.value = currentCounter; // Tampilkan nilai counter di textarea

            dateInput.value = getCurrentDateTime(); // Perbarui nilai date setiap kali input diubah

            if (systemCounterInput.value.trim() !== '') {
                loadingSpinner.style.display = 'block'; // Tampilkan animasi loading
            }

            clearTimeout(timeoutId); // Hapus timeout sebelumnya

            timeoutId = setTimeout(() => saveCounterToDatabase(), 1500); // Simpan setelah 1,5 detik
        }else if(inputValue === jamCodeInput.value && jamCodeInput.value !== "-"){
            currentCounter += 1; // Increment counter
            counterInput.value = currentCounter; // Update tampilan counter
            systemCounterLarge.value = currentCounter; // Tampilkan nilai counter di textarea

            dateInput.value = getCurrentDateTime(); // Perbarui nilai date setiap kali input diubah

            if (systemCounterInput.value.trim() !== '') {
                loadingSpinner.style.display = 'block'; // Tampilkan animasi loading
            }

            clearTimeout(timeoutId); // Hapus timeout sebelumnya

            timeoutId = setTimeout(() => saveCounterToDatabase(), 1500); // Simpan setelah 1,5 detik
        }
        else{

            clearTimeout(timeoutId); // Hapus timeout sebelumnya

            timeoutId = setTimeout(() => showNotification('Mismatch',false), 1500); // Simpan setelah 1,5 detik            
        }
        
    });

    // Fungsi untuk mengirim data ke database setelah 1 detik
    function saveCounterToDatabase() {
        const product = productSelect.value;
        const lotNumber = lotNumberInput.value;
        const date = getCurrentDateTime();

    
        const formData = new URLSearchParams();
        formData.append('product', product);
        formData.append('lot_number', lotNumber);
        formData.append('date', date);
        formData.append('counter', currentCounter);

        fetch('post_data.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log("Data berhasil dikirim ke database:", formData.toString());

                // Play success sound
                successSound.play().catch(e => console.error('Error playing sound:', e));

                if ('Notification' in window) {
                    if (Notification.permission === 'granted') {
                        new Notification('Success', {
                            body: 'Data has been successfully saved to the database',
                            icon: '../assets/icon-success.png'
                        });
                    } else if (Notification.permission !== 'denied') {
                        Notification.requestPermission().then(permission => {
                            if (permission === 'granted') {
                                new Notification('Success', {
                                    body: 'Data has been successfully saved to the database',
                                    icon: '../assets/icon-success.png'
                                });
                            }
                        });
                    }
                }
                
                // On-screen notification with icon
                const notification = document.createElement('div');
                notification.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <img src="../assets/icon-success.png" alt="Success" style="width: 24px; height: 24px;">
                        <span>Data successfully saved to database</span>
                    </div>
                `;
                notification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background-color: #4CAF50;
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
                `;
                document.body.appendChild(notification);
                setTimeout(() => notification.remove(), 2000); // Remove after 2 seconds

                // Kosongkan System Counter dan System Counter Large setelah data disimpan
                systemCounterInput.value = '';
                systemCounterLarge.value = '';
            } else {
                console.error("Error dari server:", data.error);
                alert('Terjadi kesalahan saat menambahkan data: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Terjadi kesalahan saat mengirim data: ' + error);
        })
        .finally(() => {
            loadingSpinner.style.display = 'none'; // Sembunyikan animasi setelah selesai
        });
    }

    // Event untuk tombol clear
    document.querySelector('.btn-clear').addEventListener('click', function (event) {
        event.preventDefault();
        document.querySelector('.barcode-form').reset();
        dateInput.value = ""; // Reset waktu setelah clear
        systemCounterLarge.value = '';
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const historyIcon = document.querySelector('.history-icon');

    historyIcon.addEventListener('click', function () {
        // Arahkan ke halaman log_activity.php
        window.location.href = '../log_activity/log_activity.php?from=add';
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
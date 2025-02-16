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


document.addEventListener("DOMContentLoaded", function () {
    const productDropdown = $('#productUP');
    const noLotDropdown = $('#lot-numberUP');
    const counterField = document.getElementById("counterUP");
    const processButton = document.querySelector(".btn-process");
   

    const productField = document.getElementById("product");
    const rssCodeField = document.getElementById("rss-code");
    const jamCodeField = document.getElementById("jam-code");
    const lotNumberField = document.getElementById("lot-number");
    const dateField = document.getElementById("date");
    const counterDisplayField = document.getElementById("counter");
    const systemCounterInput = document.getElementById('system-counter');
    const loadingSpinner = document.querySelector('.loading-spinner');
    const systemCounterLarge = document.getElementById('system-counter-large');


    // Create audio element for success sound
    const successSound = new Audio('../assets/success.mp3');

    let currentCounter = 0; // Menyimpan nilai counter yang ada
    let timeoutId = null; // Untuk menyimpan setTimeout ID

    // Initialize Select2 for both dropdowns
    productDropdown.select2({
        placeholder: "Select Product",
        allowClear: true,
        width: '100%'
    });

    noLotDropdown.select2({
        placeholder: "Select No. Lot",
        allowClear: true,
        width: '100%'
    });

    // Event listener saat Select2 akan dibuka
    productDropdown.on('select2:opening', function (e) {
        const selectedBuilding = document.getElementById('gedung').value; // Get value of Production Building

        // If no production building is selected, show notification and prevent dropdown from opening
        if (!selectedBuilding) {
            showNotification("Please select the Production Building first!", false);
            e.preventDefault(); // Prevent the dropdown from opening
            return; // Stop the rest of the process
        }
    });

    
     // Event listener for no lot dropdown click
     productDropdown.on('select2:opening', function(e) {
        $('#lot-numberUP').val('').trigger('change');
    });

     // Event listener for no lot dropdown click
    noLotDropdown.on('select2:opening', function(e) {
        if (!productDropdown.val()) {
            e.preventDefault();
            showNotification('Please select a Product first.', false);
        }
    });

    function getCurrentDateTime() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    }

    // Function to reset form bawah
    function resetFormBawah() {
        noLotDropdown.value = ""
        counterField.value = ""
        productField.value = "";
        rssCodeField.value = "";
        jamCodeField.value = "";
        lotNumberField.value = "";
        dateField.value = "";
        counterDisplayField.value = "";
        systemCounterInput.value = "";
        systemCounterLarge.value = "";
    }

    function resetFormBawah2() {
        productField.value = "";
        rssCodeField.value = "";
        jamCodeField.value = "";
        lotNumberField.value = "";
        dateField.value = "";
        counterDisplayField.value = "";
        systemCounterInput.value = "";
        systemCounterLarge.value = "";
    }


    // Event listener untuk reset form bawah saat product diubah di form atas
    productDropdown.on("change", function () {
        resetFormBawah();
        noLotDropdown.val('').trigger('change'); // Reset no lot setelah produk diubah
    });
    
    noLotDropdown.on("change", function () {
        resetFormBawah2();
    });


    

    systemCounterInput.addEventListener('click', () => {
        let selectedProduct = productDropdown.value;
        let selectedNoLot = noLotDropdown.value;
        let counter = counterField.value;
        let nolot = lotNumberField.value;

        if (nolot==="") {
            showNotification('Press Button "Process" to unlock this field.', false);
            systemCounterInput.blur();
        }
    });

    // Fetch and populate product list
    $(document).ready(function () {
        const productDropdown = $('#productUP');
        const noLotDropdown = $('#lot-numberUP');
        const counterField = document.getElementById("counterUP");
        const gedungDropdown = $('#gedung');
        
        async function fetchProducts() {
            const selectedGedung = gedungDropdown.val(); // Ambil nilai gedung yang dipilih
            try {
                const response = await fetch(`fetch_options.php?action=getProducts&gedung=${encodeURIComponent(selectedGedung)}`);
                const data = await response.json();

                // Bersihkan opsi lama & tambahkan opsi default
                productDropdown.empty().append('<option value="">Select Product</option>');
        
                data.forEach(product => {
                    const option = new Option(product, product);
                    productDropdown.append(option);
                });

                productDropdown.trigger('change'); // Refresh Select2
            } catch (error) {
                console.error("Error fetching products:", error);
                showNotification('Unable to load products. Please try again.', false);
            }
        }

        async function fetchNoLots() {
            try {
                const response = await fetch("fetch_options.php?action=getNoLots");
                const data = await response.json();
                
                // Clear existing options
                noLotDropdown.empty().append('<option value="">Select No. Lot</option>');
                
                data.forEach(noLot => {
                    const option = new Option(noLot, noLot);
                    noLotDropdown.append(option);
                });
                
                noLotDropdown.trigger('change');
            } catch (error) {
                console.error("Error fetching No Lots:", error);
                showNotification('Unable to load lot numbers. Please try again.', false);
            }
        }

        // Event listener untuk mengambil produk setelah gedung dipilih
        gedungDropdown.on('change', function () {
            const selectedBuilding = $(this).val();
            
            // Reset nilai produk, no lot, dan counter saat gedung dipilih
            if (selectedBuilding) {
                // Ambil produk sesuai gedung
                fetchProducts();
            } else {
                // Reset produk, no lot, dan counter jika gedung tidak dipilih
                productDropdown.empty().append('<option value="">Select Product</option>');
                noLotDropdown.empty().append('<option value="">Select No. Lot</option>');
                counterField.value = ''; // Reset counter field
                productDropdown.trigger('change');
            }
        });        

        // Panggil fetchProducts() saat halaman pertama kali dimuat jika gedung sudah dipilih
        if (gedungDropdown.val()) {
            fetchProducts();
        }

        // Initial fetch of lots
        fetchNoLots();

        // Fetch counter when No Lot is selected
        noLotDropdown.on('change', function() {
            let selectedProduct = productDropdown.val();
            let selectedNoLot = $(this).val();
            counterField.value = "";

            if (selectedProduct && selectedNoLot) {
                fetch(`fetch_options.php?action=getCounter&product=${encodeURIComponent(selectedProduct)}&no_lot=${encodeURIComponent(selectedNoLot)}`)
                    .then(response => response.json())
                    .then(data => {
                        counterField.value = data.counter || "";
                    })
                    .catch(error => {
                        console.error("Error fetching counter:", error);
                        showNotification('Unable to fetch counter. Please try again.', false);
                    });
            }
        });
    });  

    // Fetch and display process data when "Process" button is clicked
    processButton.addEventListener("click", function(e) {
        e.preventDefault();
        let selectedProduct = productDropdown.val();
        let selectedNoLot = noLotDropdown.val();
        let counter = counterField.value;

        if (!selectedProduct || !selectedNoLot || !counter) {
            showNotification('Please select Product, No. Lot, and ensure Counter is filled.', false);
            return;
        }

        fetch(`fetch_process_data.php?product=${encodeURIComponent(selectedProduct)}&no_lot=${encodeURIComponent(selectedNoLot)}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    productField.value = data.product;
                    rssCodeField.value = data.rss_code;
                    jamCodeField.value = data.jam_code || "-";
                    lotNumberField.value = data.no_lot;
                    counterDisplayField.value = data.counter;
                    dateField.value = getCurrentDateTime();

                    showNotification("Data successfully retrieved and displayed.", true);
                    systemCounterInput.focus();
                    lotNumberField.scrollIntoView({ behavior: "smooth", block: "center" });
                } else {
                    showNotification("No data found.", false);
                }
            })
            .catch(error => {
                console.error("Error fetching data:", error);
                showNotification('An error occurred while processing. Please try again.', false);
            });
    });


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





    function showNotificationMismatch(message, isSuccess) {
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
    }
 
    // Animasi CSS untuk notifikasi
    const style2 = document.createElement('style');
    style2.innerHTML = `
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
    document.head.appendChild(style2);


    //save

    loadingSpinner.classList.add('loading-spinner');

 // Event listener untuk System Counter
 let isLocked = false; // Variabel untuk mengunci input selama proses berlangsung

 systemCounterInput.addEventListener('input', function () {
     if (isLocked) return; // Jika sedang terkunci, hentikan proses
 
     const inputValue = systemCounterInput.value;
 
     if (inputValue === rssCodeField.value) {
         isLocked = true; // Kunci input
         currentCounter = Number(counterDisplayField.value)+1; // Increment counter
         counterDisplayField.value = currentCounter; // Update tampilan counter
         systemCounterLarge.value = currentCounter; // Tampilkan nilai counter di textarea
         dateField.value = getCurrentDateTime(); // Perbarui nilai date setiap kali input diubah
 
         if (systemCounterInput.value.trim() !== '') {
             loadingSpinner.style.display = 'block'; // Tampilkan animasi loading
         }
 
         clearTimeout(timeoutId); // Hapus timeout sebelumnya
         timeoutId = setTimeout(() => {
             saveCounterToDatabase();
             isLocked = false; // Buka kunci setelah proses selesai
         }, 1500); // Simpan setelah 1,5 detik
     } else if (inputValue === jamCodeField.value && jamCodeField.value !== "-") {
         isLocked = true; // Kunci input
         currentCounter = Number(counterDisplayField.value)+1; // Increment counter
         counterDisplayField.value = currentCounter; // Update tampilan counter
         systemCounterLarge.value = currentCounter; // Tampilkan nilai counter di textarea
         dateField.value = getCurrentDateTime(); // Perbarui nilai date setiap kali input diubah
 
         if (systemCounterInput.value.trim() !== '') {
             loadingSpinner.style.display = 'block'; // Tampilkan animasi loading
         }
 
         clearTimeout(timeoutId); // Hapus timeout sebelumnya
         timeoutId = setTimeout(() => {
             saveCounterToDatabase();
             isLocked = false; // Buka kunci setelah proses selesai
         }, 1500); // Simpan setelah 1,5 detik
     } else {
         clearTimeout(timeoutId); // Hapus timeout sebelumnya
         timeoutId = setTimeout(() => {
             showNotification('Mismatch RSS-Code/JAM-Code', false);
             systemCounterInput.value = ''; // Kosongkan input
         }, 1500);
     }
 });


    
    // Fungsi untuk mengirim data ke database setelah 1 detik
    function saveCounterToDatabase() {
        const product = productField.value;
        const lotNumber = lotNumberField.value;
        const date = getCurrentDateTime();

        const formData = new URLSearchParams();
        formData.append('product', product);
        formData.append('lot_number', lotNumber);
        formData.append('date', date);
        formData.append('counter', currentCounter);

        fetch('post_sequence_data.php', {
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
        
        // Reset form utama
        document.querySelector('.barcode-form').reset();

        // Reset semua input field
        productField.value = "";
        rssCodeField.value = "";
        jamCodeField.value = "";
        lotNumberField.value = "";
        dateField.value = "";
        counterDisplayField.value = "";
        systemCounterInput.value = "";
        systemCounterLarge.value = "";
        
        // Reset Select2 untuk ProductUP dan Lot-numberUP
        $('#productUP').val('').trigger('change');
        $('#lot-numberUP').val('').trigger('change');


        // Scroll ke tombol "Process"
        processButton.scrollIntoView({ behavior: "smooth", block: "center" });
    });

});



//others

document.addEventListener('DOMContentLoaded', function () {
    const historyIcon = document.querySelector('.history-icon');

    historyIcon.addEventListener('click', function () {
        // Arahkan ke halaman log_activity.php
        window.location.href = '../log_activity/log_activity.php?from=add-edit';
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
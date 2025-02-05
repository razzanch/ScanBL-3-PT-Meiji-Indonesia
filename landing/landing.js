// Ambil elemen yang diperlukan
const imageContainer = document.querySelector('.image-container img');
const overlayContainer = document.querySelector('.overlay-container');
const overlayText = document.querySelector('.overlay-container p'); // Tambahan untuk teks
const indicators = document.querySelectorAll('.indicator');
const arrowLeft = document.querySelector('.arrow-left');
const arrowRight = document.querySelector('.arrow-right');

// Data gambar, posisi overlay, dan teks
const slides = [
    {
        image: '../assets/BLMeiji.png',
        overlayLeft: '13%',
        text: 'ScanBL3 adalah aplikasi berbasis web yang dirancang untuk memindai label barcode pada produk PT Meiji Indonesia, memberikan informasi lengkap mengenai jenis, jumlah, dan detail produk dengan cepat dan akurat.'
    },
    {
        image: '../assets/officeMeiji.png',
        overlayLeft: '65%',
         text: 'ScanBL3 adalah aplikasi berbasis web yang dirancang untuk memindai label barcode pada produk PT Meiji Indonesia, memberikan informasi lengkap mengenai jenis, jumlah, dan detail produk dengan cepat dan akurat.'
    },
];

// Variable untuk melacak slide saat ini
let currentSlide = 0;

// Modifikasi JavaScript
function changeSlide(index) {
    // Validasi index yang sudah ada
    if (index < 0) index = slides.length - 1;
    if (index >= slides.length) index = 0;

    // Reset animasi sebelum mengganti gambar
    imageContainer.style.animation = 'none';
    imageContainer.offsetHeight; // Trigger reflow
    imageContainer.style.animation = null;

    // Tambahkan kelas animasi untuk fade out
    imageContainer.classList.add('image-fade-out');
    overlayContainer.classList.add('slide-out');

    setTimeout(() => {
        // Update gambar dan posisi
        imageContainer.src = slides[index].image;
        overlayContainer.style.left = slides[index].overlayLeft;
        
        // Mulai animasi vertikal
        imageContainer.style.animation = 'verticalLoopAnimation 10s ease-in-out infinite';
        
        // Kode yang sudah ada untuk overlay text
        overlayText.style.opacity = '0';
        overlayText.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            overlayText.textContent = slides[index].text;
            overlayText.style.opacity = '1';
            overlayText.style.transform = 'translateY(0)';
        }, 200);

        // Hapus kelas fade out dan tambahkan fade in
        imageContainer.classList.remove('image-fade-out');
        imageContainer.classList.add('image-fade-in');
        overlayContainer.classList.remove('slide-out');
        overlayContainer.classList.add('slide-in');

        // Update indikator
        indicators.forEach((indicator, i) => {
            indicator.classList.toggle('active', i === index);
        });

        // Reset animasi gambar
        setTimeout(() => {
            imageContainer.classList.remove('image-fade-in');
        }, 400);
    }, 400);

    currentSlide = index;
}

// Tambahkan auto-play (opsional)
let slideInterval = setInterval(() => {
    changeSlide(currentSlide + 1);
}, 5000); // Ganti slide setiap 5 detik

// Reset interval saat user mengklik tombol
function resetInterval() {
    clearInterval(slideInterval);
    slideInterval = setInterval(() => {
        changeSlide(currentSlide + 1);
    }, 5000);
}

// Update event listeners
arrowLeft.addEventListener('click', () => {
    changeSlide(currentSlide - 1);
    resetInterval();
});

arrowRight.addEventListener('click', () => {
    changeSlide(currentSlide + 1);
    resetInterval();
});

indicators.forEach((indicator, index) => {
    indicator.addEventListener('click', () => {
        changeSlide(index);
        resetInterval();
    });
});
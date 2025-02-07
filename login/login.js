document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.querySelector("form");
    const passwordToggle = document.getElementById("passwordToggle");

    function sanitizeInput(input) {
        const forbiddenChars = /[<>&"'=/]/g;
        if (forbiddenChars.test(input)) {
            return null;
        }
        return input;
    }

    loginForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Mencegah reload halaman

        let username = document.getElementById("username").value;
        let password = document.getElementById("password").value;

        // Validasi input
        if (!sanitizeInput(username) || !sanitizeInput(password)) {
            showNotification("Username/Password must not contain prohibited characters! (<, >, &, \", ', /, =)", "error");
            return;
        }

        // Kirim data ke checklogin.php menggunakan fetch API
        fetch("checklogin.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ username, password }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification("Login Berhasil!", "success");
                console.log("User Logged In:", data.user);
                setTimeout(() => {
                    window.location.href = "../dashboard/dashboard.php";
                }, 1500);
            } else {
                showNotification(data.error, "error");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            showNotification("Terjadi kesalahan saat login!", "error");
        });
    });

    if (passwordToggle) {
        const passwordInput = document.getElementById("password"); // ✅ Pastikan elemen ditemukan
    
        passwordToggle.addEventListener("click", function() {
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordToggle.src = "../assets/show-pw.png";
            } else {
                passwordInput.type = "password";
                passwordToggle.src = "../assets/hide-pw.png";
            }
        });
    }
});

// Fungsi menampilkan notifikasi
function showNotification(message, type = "success") {
    const notification = document.createElement("div");
    const iconSrc = type === "success" ? "../assets/icon-success.png" : "../assets/icon-error.png";
    const bgColor = type === "success" ? "#4CAF50" : "#f44336";

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

    document.body.appendChild(notification);
    setTimeout(() => {
        notification.style.opacity = "1";
    }, 100);

    setTimeout(() => {
        notification.style.opacity = "0";
        setTimeout(() => document.body.removeChild(notification), 500);
    }, 3000);
}

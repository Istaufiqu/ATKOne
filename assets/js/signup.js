document.addEventListener("DOMContentLoaded", function () {
    const signUpForm = document.querySelector("#sign-up-form");
    const signUpButton = document.querySelector(".signup");

    // Pastikan tombol Sign Up bisa diklik
    if (signUpButton) {
        signUpButton.disabled = false;
        signUpButton.style.opacity = "1";
    }

    // Fungsi untuk menampilkan notifikasi
    function showNotification(message, type) {
        const notification = document.createElement("div");
        notification.className = `notification ${type}`;
        notification.innerText = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // 🚀 Handle Sign Up (Pendaftaran)
    if (signUpForm) {
        signUpForm.addEventListener("submit", function (event) {
            event.preventDefault();

            const name = document.querySelector("#signup-name").value.trim();
            const email = document.querySelector("#signup-email").value.trim();
            const phone = document.querySelector("#signup-phone").value.trim();
            const password = document.querySelector("#signup-password").value.trim();

            // Validasi email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showNotification("Format email tidak valid!", "error");
                return;
            }

            // Cek apakah semua kolom terisi
            if (!name || !email || !phone || !password) {
                showNotification("Harap isi semua kolom dengan benar!", "error");
                return;
            }

            // Kirim data ke server
            fetch("http://localhost/atk_apl/signup.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `signup-name=${encodeURIComponent(name)}&signup-email=${encodeURIComponent(email)}&signup-phone=${encodeURIComponent(phone)}&signup-password=${encodeURIComponent(password)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    showNotification(data.message, "success");

                    // Simpan data ke localStorage (opsional, tergantung kebutuhan)
                    localStorage.setItem("userName", name);
                    localStorage.setItem("userEmail", email);
                    localStorage.setItem("userPhone", phone);

                    // Redirect ke halaman login setelah sukses
                    setTimeout(() => {
                        window.location.href = "index.html";
                    }, 2000);
                } else {
                    showNotification(data.message, "error");
                }
            })
            .catch(error => {
                console.error("Error Fetch:", error);
                showNotification("Terjadi kesalahan saat menghubungi server!", "error");
            });
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const signInForm = document.querySelector("#sign-in-form");
    const signInButton = document.querySelector("#sign-in-button");
    const emailInput = document.querySelector("#signin-email");
    const passwordInput = document.querySelector("#signin-password");
    const errorMessage = document.querySelector("#error-message");

    function showNotification(message, type) {
        errorMessage.textContent = message;
        errorMessage.style.color = type === "error" ? "red" : "green";
    }

    function validateInput() {
        signInButton.disabled = emailInput.value.trim() === "" || passwordInput.value.trim() === "";
    }

    emailInput.addEventListener("input", validateInput);
    passwordInput.addEventListener("input", validateInput);
    validateInput(); // Cek input saat pertama dimuat

    signInForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData(signInForm);

        fetch("signin.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification("Login berhasil! Mengalihkan...", "success");
                setTimeout(() => {
                    window.location.href = "dashboard.html"; // Arahkan ke halaman utama jika login berhasil
                }, 2000);
            } else {
                showNotification(data.error, "error");
                emailInput.value = "";
                passwordInput.value = "";
                validateInput();
            }
        })
        .catch(error => {
            console.error("Fetch Error:", error);
            showNotification("Terjadi kesalahan saat menghubungi server!", "error");
        });
    });
});

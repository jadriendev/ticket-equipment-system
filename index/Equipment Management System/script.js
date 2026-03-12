const togglePassword = document.getElementById("togglePassword");
        const password = document.getElementById("password");
        togglePassword.addEventListener("click", () => {
            const type = password.type === "password" ? "text" : "password";
            password.type = type;
            togglePassword.classList.toggle("fa-eye");
});
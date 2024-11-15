function validateLogin() {
    const email = document.getElementById("e-mail").value.trim();
    const password = document.getElementById("password").value.trim();

    let isValid = true;

    document.getElementById("e-mail").nextElementSibling.textContent = "";

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!email || !emailPattern.test(email)) {
        document.getElementById("e-mail").nextElementSibling.textContent = "Wprowadź poprawny e-mail.";
        isValid = false;
    }

    if (!password) {
        document.getElementById("password").nextElementSibling.textContent = "Hasło jest wymagane.";
        isValid = false;
    }

    return isValid;
}
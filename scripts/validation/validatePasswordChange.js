function validatePasswordChange() {
    const currentPassword = document.getElementById("current_password").value.trim();
    const newPassword = document.getElementById("new_password").value.trim();
    const repeatedNewPassword = document.getElementById("repeated_new_password").value.trim();

    let isValid = true;

    document.getElementById("current_password").nextElementSibling.textContent = "";
    document.getElementById("new_password").nextElementSibling.textContent = "";
    document.getElementById("repeated_new_password").nextElementSibling.textContent = "";

    const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

    if (!currentPassword) {
        document.getElementById("current_password").nextElementSibling.textContent = "Aktualne hasło jest wymagane.";
        isValid = false;
    }

    if (!newPassword) {
        document.getElementById("new_password").nextElementSibling.textContent = "Nowe hasło jest wymagane.";
        isValid = false;
    } else if (!passwordPattern.test(newPassword)) {
        document.getElementById("new_password").nextElementSibling.textContent = "Hasło musi mieć co najmniej 8 znaków, jedną dużą literę, jedną małą literę, jedną cyfrę i jeden znak specjalny.";
        isValid = false;
    }

    if (newPassword !== repeatedNewPassword) {
        document.getElementById("repeated_new_password").nextElementSibling.textContent = "Nowe hasła muszą być zgodne.";
        isValid = false;
    }

    return isValid;
}
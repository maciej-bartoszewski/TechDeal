function validateRegistration() {
    const firstName = document.getElementById("first_name").value.trim();
    const lastName = document.getElementById("last_name").value.trim();
    const email = document.getElementById("e-mail").value.trim();
    const phoneNumber = document.getElementById("phone_number").value.trim();
    const password = document.getElementById("password").value.trim();
    const repeatedPassword = document.getElementById("repeated_password").value.trim();

    let isValid = true;

    document.getElementById("first_name").nextElementSibling.textContent = "";
    document.getElementById("last_name").nextElementSibling.textContent = "";
    document.getElementById("e-mail").nextElementSibling.textContent = "";
    document.getElementById("phone_number").nextElementSibling.textContent = "";
    document.getElementById("password").nextElementSibling.textContent = "";
    document.getElementById("repeated_password").nextElementSibling.textContent = "";

    const namePattern = /^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+$/;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phonePattern = /^\d{9}$/;
    const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

    if (!firstName) {
        document.getElementById("first_name").nextElementSibling.textContent = "Imię jest wymagane.";
        isValid = false;
    } else if (!namePattern.test(firstName)) {
        document.getElementById("first_name").nextElementSibling.textContent = "Imię może zawierać tylko litery.";
        isValid = false;
    }

    if (!lastName) {
        document.getElementById("last_name").nextElementSibling.textContent = "Nazwisko jest wymagane.";
        isValid = false;
    } else if (!namePattern.test(lastName)) {
        document.getElementById("last_name").nextElementSibling.textContent = "Nazwisko może zawierać tylko litery.";
        isValid = false;
    }

    if (!email || !emailPattern.test(email)) {
        document.getElementById("e-mail").nextElementSibling.textContent = "Wprowadź poprawny e-mail.";
        isValid = false;
    }

    if (!phoneNumber || !phonePattern.test(phoneNumber)) {
        document.getElementById("phone_number").nextElementSibling.textContent = "Wprowadź poprawny numer telefonu (9 cyfr).";
        isValid = false;
    }

    if (!password || !passwordPattern.test(password)) {
        document.getElementById("password").nextElementSibling.textContent = "Hasło musi mieć co najmniej 8 znaków, jedną dużą literę, jedną małą literę, jedną cyfrę i jeden znak specjalny.";
        isValid = false;
    }

    if (password !== repeatedPassword) {
        document.getElementById("repeated_password").nextElementSibling.textContent = "Hasła nie są zgodne.";
        isValid = false;
    }

    return isValid;
}
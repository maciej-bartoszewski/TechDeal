function validateUserAdd() {
    const firstName = document.getElementById("first_name").value.trim();
    const lastName = document.getElementById("last_name").value.trim();
    const email = document.getElementById("email").value.trim();
    const phoneNumber = document.getElementById("phone_number").value.trim();
    const password = document.getElementById("password").value.trim();
    const repeatedPassword = document.getElementById("repeated_password").value.trim();
    const accountType = document.getElementById("account_type").value;

    let isValid = true;

    document.getElementById("first_name").nextElementSibling.textContent = "";
    document.getElementById("last_name").nextElementSibling.textContent = "";
    document.getElementById("email").nextElementSibling.textContent = "";
    document.getElementById("phone_number").nextElementSibling.textContent = "";
    document.getElementById("password").nextElementSibling.textContent = "";
    document.getElementById("repeated_password").nextElementSibling.textContent = "";
    document.getElementById("account_type").nextElementSibling.textContent = "";

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
        document.getElementById("email").nextElementSibling.textContent = "Niepoprawny adres e-mail.";
        isValid = false;
    }

    if (!phoneNumber || !phonePattern.test(phoneNumber)) {
        document.getElementById("phone_number").nextElementSibling.textContent = "Numer telefonu musi mieć 9 cyfr.";
        isValid = false;
    }

    if (!password) {
        document.getElementById("password").nextElementSibling.textContent = "Hasło jest wymagane.";
        isValid = false;
    } else if (!passwordPattern.test(password)) {
        document.getElementById("password").nextElementSibling.textContent = "Hasło musi mieć co najmniej 8 znaków, jedną dużą literę, jedną małą literę, jedną cyfrę i jeden znak specjalny.";
        isValid = false;
    }

    if (!repeatedPassword) {
        document.getElementById("repeated_password").nextElementSibling.textContent = "Powtórz hasło.";
        isValid = false;
    } else if (password !== repeatedPassword) {
        document.getElementById("repeated_password").nextElementSibling.textContent = "Hasła muszą być identyczne.";
        isValid = false;
    }

    if (!accountType) {
        document.getElementById("account_type").nextElementSibling.textContent = "Typ konta jest wymagany.";
        isValid = false;
    }

    return isValid;
}
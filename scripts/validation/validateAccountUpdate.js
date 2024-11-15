function validateAccountUpdate() {
    const firstName = document.getElementById("first_name").value.trim();
    const lastName = document.getElementById("last_name").value.trim();
    const email = document.getElementById("e_mail").value.trim();
    const phoneNumber = document.getElementById("phone_number").value.trim();

    let isValid = true;

    document.getElementById("first_name").nextElementSibling.textContent = "";
    document.getElementById("last_name").nextElementSibling.textContent = "";
    document.getElementById("e_mail").nextElementSibling.textContent = "";
    document.getElementById("phone_number").nextElementSibling.textContent = "";

    const namePattern = /^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+$/;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phonePattern = /^\d{9}$/;

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
        document.getElementById("e_mail").nextElementSibling.textContent = "Niepoprawny adres e-mail.";
        isValid = false;
    }

    if (!phoneNumber || !phonePattern.test(phoneNumber)) {
        document.getElementById("phone_number").nextElementSibling.textContent = "Numer telefonu musi mieć 9 cyfr.";
        isValid = false;
    }

    return isValid;
}
function validateAddress() {
    const country = document.getElementById("country").value.trim();
    const street = document.getElementById("street").value.trim();
    const buildingNumber = document.getElementById("building_number").value.trim();
    const apartmentNumber = document.getElementById("apartment_number").value.trim();
    const postCode = document.getElementById("post_code").value.trim();
    const city = document.getElementById("city").value.trim();

    let isValid = true;

    document.getElementById("country").nextElementSibling.textContent = "";
    document.getElementById("street").nextElementSibling.textContent = "";
    document.getElementById("building_number").nextElementSibling.textContent = "";
    document.getElementById("apartment_number").nextElementSibling.textContent = "";
    document.getElementById("post_code").nextElementSibling.textContent = "";
    document.getElementById("city").nextElementSibling.textContent = "";

    const namePattern = /^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+$/;
    const postCodePattern = /^\d{2}-\d{3}$/;

    if (!country) {
        document.getElementById("country").nextElementSibling.textContent = "Kraj jest wymagany.";
        isValid = false;
    } else if (!namePattern.test(country)) {
        document.getElementById("country").nextElementSibling.textContent = "Kraj może zawierać tylko litery.";
        isValid = false;
    }

    if (!street) {
        document.getElementById("street").nextElementSibling.textContent = "Ulica jest wymagana.";
        isValid = false;
    }

    if (!buildingNumber) {
        document.getElementById("building_number").nextElementSibling.textContent = "Numer budynku jest wymagany.";
        isValid = false;
    } else if (isNaN(buildingNumber) || buildingNumber < 1) {
        document.getElementById("building_number").nextElementSibling.textContent = "Numer budynku musi być liczbą.";
        isValid = false;
    }

    if (apartmentNumber && (isNaN(apartmentNumber) || apartmentNumber < 1)) {
        document.getElementById("apartment_number").nextElementSibling.textContent = "Numer mieszkania musi być liczbą.";
        isValid = false;
    }

    if (!postCode) {
        document.getElementById("post_code").nextElementSibling.textContent = "Kod pocztowy jest wymagany.";
        isValid = false;
    } else if (!postCodePattern.test(postCode)) {
        document.getElementById("post_code").nextElementSibling.textContent = "Kod pocztowy musi być w formacie XX-XXX.";
        isValid = false;
    }

    if (!city) {
        document.getElementById("city").nextElementSibling.textContent = "Miasto jest wymagane.";
        isValid = false;
    } else if (!namePattern.test(city)) {
        document.getElementById("city").nextElementSibling.textContent = "Miasto może zawierać tylko litery.";
        isValid = false;
    }

    return isValid;
}
function validatePaymentAddEdit() {
    const paymentMethod = document.getElementById("payment_method").value.trim();
    const imagePath = document.getElementById("image_path").value.trim();

    let isValid = true;

    document.getElementById("payment_method").nextElementSibling.textContent = "";
    document.getElementById("image_path").nextElementSibling.textContent = "";

    if (!paymentMethod) {
        document.getElementById("payment_method").nextElementSibling.textContent = "Metoda płatności jest wymagana.";
        isValid = false;
    }

    if (!imagePath) {
        document.getElementById("image_path").nextElementSibling.textContent = "Ścieżka do obrazu jest wymagana.";
        isValid = false;
    }

    return isValid;
}
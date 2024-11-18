function validateProducerAddEdit() {
    const producerName = document.getElementById("producer_name").value.trim();
    const imagePath = document.getElementById("image_path").value.trim();

    let isValid = true;

    document.getElementById("producer_name").nextElementSibling.textContent = "";
    document.getElementById("image_path").nextElementSibling.textContent = "";

    if (!producerName) {
        document.getElementById("producer_name").nextElementSibling.textContent = "Nazwa producenta jest wymagana.";
        isValid = false;
    }

    if (!imagePath) {
        document.getElementById("image_path").nextElementSibling.textContent = "Ścieżka do obrazu jest wymagana.";
        isValid = false;
    }

    return isValid;
}
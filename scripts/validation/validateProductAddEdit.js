function validateProductAddEdit() {
    const categoryId = document.getElementById("category_id").value.trim();
    const producerId = document.getElementById("producer_id").value.trim();
    const productName = document.getElementById("product_name").value.trim();
    const description = document.getElementById("description").value.trim();
    const specification = document.getElementById("specification").value.trim();
    const price = document.getElementById("price").value.trim();
    const stockQuantity = document.getElementById("stock_quantity").value.trim();
    const imagePath = document.getElementById("image_path").value.trim();

    let isValid = true;

    document.getElementById("category_id").nextElementSibling.textContent = "";
    document.getElementById("producer_id").nextElementSibling.textContent = "";
    document.getElementById("product_name").nextElementSibling.textContent = "";
    document.getElementById("description").nextElementSibling.textContent = "";
    document.getElementById("specification").nextElementSibling.textContent = "";
    document.getElementById("price").nextElementSibling.textContent = "";
    document.getElementById("stock_quantity").nextElementSibling.textContent = "";
    document.getElementById("image_path").nextElementSibling.textContent = "";

    if (!categoryId) {
        document.getElementById("category_id").nextElementSibling.textContent = "Kategoria jest wymagana.";
        isValid = false;
    }

    if (!producerId) {
        document.getElementById("producer_id").nextElementSibling.textContent = "Producent jest wymagany.";
        isValid = false;
    }

    if (!productName) {
        document.getElementById("product_name").nextElementSibling.textContent = "Nazwa produktu jest wymagana.";
        isValid = false;
    }

    if (!description) {
        document.getElementById("description").nextElementSibling.textContent = "Opis produktu jest wymagany.";
        isValid = false;
    }

    if (!specification) {
        document.getElementById("specification").nextElementSibling.textContent = "Specyfikacja produktu jest wymagana.";
        isValid = false;
    }

    if (!price || isNaN(price) || price < 0) {
        document.getElementById("price").nextElementSibling.textContent = "Cena produktu jest wymagana, musi być liczbą i nie może być mniejsza niż 0.";
        isValid = false;
    }

    if (!stockQuantity || isNaN(stockQuantity) || stockQuantity < 0) {
        document.getElementById("stock_quantity").nextElementSibling.textContent = "Stan magazynowy jest wymagany, musi być liczbą i nie może być mniejszy niż 0.";
        isValid = false;
    }

    if (!imagePath) {
        document.getElementById("image_path").nextElementSibling.textContent = "Ścieżka do obrazu jest wymagana.";
        isValid = false;
    }

    return isValid;
}
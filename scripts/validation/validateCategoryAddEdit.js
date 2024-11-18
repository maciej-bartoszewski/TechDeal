function validateCategoryAddEdit() {
    const categoryName = document.getElementById("category_name").value.trim();

    let isValid = true;

    document.getElementById("category_name").nextElementSibling.textContent = "";

    if (!categoryName) {
        document.getElementById("category_name").nextElementSibling.textContent = "Nazwa kategorii jest wymagana.";
        isValid = false;
    }

    return isValid;
}
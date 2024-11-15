function showAlert(message, type = "info") {
    const alertContainer = document.getElementById("alert-container");
    const alert = document.createElement("div");
    alert.classList.add("alert", type);
    alert.textContent = message;

    alertContainer.appendChild(alert);
    requestAnimationFrame(() => alert.classList.add("show"));

    setTimeout(() => {
        alert.classList.remove("show");
        alert.addEventListener("transitionend", () => alert.remove());
    }, 3000);
}
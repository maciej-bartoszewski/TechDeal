function saveAddress(addressId, element) {
    document.getElementById("selected_address_id").value = addressId;

    const addresses = document.querySelectorAll(".address");
    addresses.forEach(function (addr) {
        addr.classList.remove("active");
    });

    element.classList.add("active");
}

function savePayment(paymentId, element) {
    document.getElementById("selected_payment_id").value = paymentId;

    const payments = document.querySelectorAll(".payment_method");
    payments.forEach(function (payment) {
        payment.classList.remove("active");
    });

    element.classList.add("active");
}
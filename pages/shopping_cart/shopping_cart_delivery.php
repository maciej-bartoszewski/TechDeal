<?php
require 'db_connect.php';
global $mysqli;

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user_id = $_SESSION['user_id'];

// Pobranie informacji o adresach użytkownika z bazy
$stmt = $mysqli->prepare("SELECT address_id, country, street, building_number, apartment_number, post_code, city FROM addresses WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$address_id = $country = $street = $building_number = $apartment_number = $postal_code = $city = null;
$stmt->bind_result($address_id, $country, $street, $building_number, $apartment_number, $postal_code, $city);

// Zapisanie adresów w tabeli $addresses
$addresses = [];
while ($stmt->fetch()) {
    $addresses[] = [
        'address_id' => $address_id,
        'country' => $country,
        'street' => $street,
        'building_number' => $building_number,
        'apartment_number' => $apartment_number ?: '-',
        'postal_code' => $postal_code,
        'city' => $city,
    ];
}
$stmt->close();

// Pobranie informacji o metodach płatności z bazy
$payment_stmt = $mysqli->prepare("SELECT payment_id, payment_method, image_path FROM payments");
$payment_stmt->execute();
$payment_id = $payment_method = $image_path = null;
$payment_stmt->bind_result($payment_id, $payment_method, $image_path);

// Zapisanie metod płatności w tabeli $payment_methods
$payment_methods = [];
while ($payment_stmt->fetch()) {
    $payment_methods[] = [
        'payment_id' => $payment_id,
        'payment_method' => $payment_method,
        'image_path' => $image_path,
    ];
}
$payment_stmt->close();

$default_address_id = $addresses[0]['address_id'] ?? 0;
$default_payment_id = $payment_methods[0]['payment_id'] ?? 0;

// Pobranie danych z bazy, dotyczących produktów w koszyku i zapisanie ich w tabeli $cartItems
$query = "SELECT p.product_id, p.product_name, p.image_path, p.price, c.quantity
          FROM cart_items c
          JOIN products p ON c.product_id = p.product_id
          WHERE c.cart_id = (SELECT cart_id FROM carts WHERE user_id = ?)";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Zapisanie produktów w tabeli $cartItems
$cartItems = [];
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
}
$stmt->close();

// Zliczenie wartości zamówienia
$totalPrice = 0;
foreach ($cartItems as $item) {
    $itemTotalPrice = (float)$item['price'] * (int)$item['quantity'];
    $totalPrice += $itemTotalPrice;
}
?>

<h2>Twój koszyk</h2>
<div class="main_cart_container">
    <div class="left_shopping_cart_container">
        <h3>Wybierz adres dostawy:</h3>
        <div class="addresses">
            <div class="new_address">
                <a href="index.php?page=account&subpage=address_add">
                    <img src="assets/icons/add.png" alt="Ikonka dodawania"/>Dodaj
                </a>
            </div>
            <?php foreach ($addresses as $index => $address): ?>
                <div class="address <?= $index === 0 ? 'active' : 'non_active' ?>"
                     data-address-id="<?= htmlspecialchars($address['address_id'], ENT_QUOTES, 'UTF-8') ?>"
                     onclick="saveAddress(<?= htmlspecialchars($address['address_id'], ENT_QUOTES, 'UTF-8') ?>, this)">
                    <div class="single_info">
                        <h4>Kraj:</h4>
                        <p><?= htmlspecialchars($address['country'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="single_info">
                        <h4>Ulica:</h4>
                        <p><?= htmlspecialchars($address['street'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="single_info">
                        <h4>Numer budynku:</h4>
                        <p><?= htmlspecialchars($address['building_number'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="single_info">
                        <h4>Numer mieszkania:</h4>
                        <p><?= htmlspecialchars($address['apartment_number'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="single_info">
                        <h4>Kod pocztowy:</h4>
                        <p><?= htmlspecialchars($address['postal_code'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="single_info">
                        <h4>Miasto:</h4>
                        <p><?= htmlspecialchars($address['city'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <h3>Wybierz sposób płatności:</h3>
        <div class="payment_methods">
            <?php foreach ($payment_methods as $index => $payment): ?>
                <div class="payment_method <?= $index === 0 ? 'active' : 'non_active' ?>"
                     data-payment-id="<?= htmlspecialchars($payment['payment_id'], ENT_QUOTES, 'UTF-8') ?>"
                     onclick="savePayment(<?= htmlspecialchars($payment['payment_id'], ENT_QUOTES, 'UTF-8') ?>, this)">
                    <img src="<?= htmlspecialchars($payment['image_path'], ENT_QUOTES, 'UTF-8') ?>"
                         alt="<?= htmlspecialchars($payment['payment_method'], ENT_QUOTES, 'UTF-8') ?>"/>
                    <p><?= htmlspecialchars($payment['payment_method'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="right_shopping_cart_container">
        <div class="price_summary_container">
            <div class="upper_price_summary">
                <div class="items_price_summary">
                    <h3>Wartość produktów:</h3>
                    <p><?= htmlspecialchars(number_format($totalPrice, 2, ',', ' '), ENT_QUOTES, 'UTF-8') ?> zł</p>
                </div>
                <div class="items_price_summary">
                    <h3>Dostawa:</h3>
                    <p>0,00 zł</p>
                </div>
            </div>
            <hr/>
            <div class="price_summary">
                <h3>Razem z dostawą:</h3>
                <p><?= htmlspecialchars(number_format($totalPrice, 2, ',', ' '), ENT_QUOTES, 'UTF-8') ?> zł</p>
            </div>
        </div>
        <form method="POST" action="pages/shopping_cart/place_order.php">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="total_price" value="<?= htmlspecialchars($totalPrice, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="address_id" id="selected_address_id"
                   value="<?= htmlspecialchars($default_address_id, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="payment_id" id="selected_payment_id"
                   value="<?= htmlspecialchars($default_payment_id, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="gray_btn">Złóż zamówienie</button>
        </form>
    </div>
</div>
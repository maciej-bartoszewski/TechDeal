<?php
require 'db_connect.php';
global $mysqli;

$order_id = $_GET['order_id'];

// Pobranie informacji o zamówieniu
$order_query = "
    SELECT o.order_date, o.total_price, o.country, o.city, o.post_code, o.street, o.building_number, o.apartment_number, p.payment_method, p.image_path AS payment_image_path
    FROM orders o
    JOIN payments p ON o.payment_id = p.payment_id
    WHERE o.order_id = ?";
$stmt = $mysqli->prepare($order_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_details = $stmt->get_result();
$order_details = $order_details->fetch_assoc();

// Pobranie informacji o produktach w zamówieniu
$items_query = "
    SELECT oi.quantity, pr.product_name, pr.price, pr.image_path
    FROM order_items oi
    JOIN products pr ON oi.product_id = pr.product_id
    WHERE oi.order_id = ?";

$stmt = $mysqli->prepare($items_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_items = $stmt->get_result();
$order_items = $order_items->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="order">
    <div class="order_items">
        <div class="order_info">
            <h4 class="bigger_h4">Nr. zamówienia: <?= htmlspecialchars($order_id) ?></h4>
            <h4 class="bigger_h4">Data
                zamówienia: <?= htmlspecialchars(date('d.m.Y', strtotime($order_details['order_date']))) ?></h4>
        </div>
        <hr/>
        <h4 class="bigger_h4">Zamówione produkty:</h4>
        <?php foreach ($order_items as $item): ?>
            <div class="item_datails_od">
                <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="Zdjęcie produktu"/>
                <h4><?= htmlspecialchars($item['product_name']) ?></h4>
            </div>
            <div class="item_price_details_od">
                <p>Cena za sztukę: <?= htmlspecialchars(number_format($item['price'], 2, ',', ' ')) ?> zł</p>
                <p>Ilość: <?= htmlspecialchars($item['quantity']) ?></p>
                <h4>Łączna cena: <?= htmlspecialchars(number_format($item['price'] * $item['quantity'], 2, ',', ' ')) ?>
                    zł</h4>
            </div>
            <hr/>
        <?php endforeach; ?>
    </div>
    <h4 class="bigger_h4 price_summary_od">Łączna cena
        zamówienia: <?= htmlspecialchars(number_format($order_details['total_price'], 2, ',', ' ')) ?> zł</h4>
    <hr/>
    <h4 class="bigger_h4">Adres dostawy:</h4>
    <div class="address_details_od">
        <p>Kraj: <?= htmlspecialchars($order_details['country']) ?></p>
        <p>Miasto: <?= htmlspecialchars($order_details['city']) ?></p>
        <p>Kod pocztowy: <?= htmlspecialchars($order_details['post_code']) ?></p>
        <p>Ulica: <?= htmlspecialchars($order_details['street']) ?></p>
        <p>Nr budynku: <?= htmlspecialchars($order_details['building_number']) ?></p>
        <p>Nr mieszkania: <?= htmlspecialchars($order_details['apartment_number'] ?? '-') ?></p>
    </div>
    <hr/>
    <h4 class="bigger_h4">Metoda płatności:</h4>
    <div class="payment_method_od">
        <img src="<?= htmlspecialchars($order_details['payment_image_path']) ?>"
             alt="<?= htmlspecialchars($order_details['payment_method']) ?>"/>
        <p><?= htmlspecialchars($order_details['payment_method']) ?></p>
    </div>
</div>
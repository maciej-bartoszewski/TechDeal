<?php
require 'db_connect.php';
global $mysqli;

$user_id = $_SESSION['user_id'];

// Pobranie informacji o zamówieniach użytkownika
$query = "
    SELECT o.order_id, o.order_date, o.total_price, oi.quantity, p.product_id, p.product_name, p.image_path, p.price
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.product_id
    WHERE o.user_id = ?
    ORDER BY o.order_date DESC
";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Dodanie informacji o produktach do tabeli $grouped_orders
$grouped_orders = [];
while ($order = $result->fetch_assoc()) {
    $grouped_orders[$order['order_id']]['order_date'] = $order['order_date'];
    $grouped_orders[$order['order_id']]['total_price'] = $order['total_price'];
    $grouped_orders[$order['order_id']]['items'][] = [
        'product_id' => $order['product_id'],
        'product_name' => $order['product_name'],
        'image_path' => $order['image_path'],
        'quantity' => $order['quantity'],
        'price' => $order['price'],
    ];
}
?>

<h3>Zamówienia</h3>
<div class="orders">
    <?php if (empty($grouped_orders)): ?>
        <p>Nie masz żadnych zamówień.</p>
    <?php else: ?>
        <?php foreach ($grouped_orders as $order_id => $order): ?>
            <div class="order">
                <div class="order_items">
                    <div class="order_info">
                        <p>Nr. zamówienia: <?= htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8') ?></p>
                        <p>Data zamówienia: <?= htmlspecialchars(date('d.m.Y', strtotime($order['order_date'])), ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <hr/>
                    <?php foreach ($order['items'] as $item): ?>
                        <div class="order_item">
                            <div class="item_datails">
                                <img src="<?= htmlspecialchars($item['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="Zdjęcie produktu"/>
                                <h4><?= htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8') ?></h4>
                            </div>
                            <div class="item_price_details">
                                <h4><?= htmlspecialchars(number_format($item['price'], 2, ',', ' '), ENT_QUOTES, 'UTF-8') ?>zł</h4>
                                <p>Ilość: <?= htmlspecialchars($item['quantity'], ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <hr/>
                    <div class="bottom_container">
                        <h4><a class="order_details_btn"
                               href="index.php?page=account&subpage=order_details&order_id=<?= htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8') ?>">Szczegóły
                                zamówienia</a></h4>
                        <h4>Łącznie: <?= htmlspecialchars(number_format($order['total_price'], 2, ',', ' '), ENT_QUOTES, 'UTF-8') ?>zł</h4>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php
require 'db_connect.php';
global $mysqli;

$search_order_id = $_GET['order_id'] ?? '';

// Przygotowywanie zapytania do bazy
$query = "
    SELECT o.order_id, o.order_date, o.total_price, oi.quantity, p.product_id, p.product_name, p.image_path, p.price
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.product_id
";

if ($search_order_id) {
    $query .= " WHERE o.order_id = ? ";
}

$query .= "ORDER BY o.order_date DESC";

$stmt = $mysqli->prepare($query);
if ($search_order_id) {
    $stmt->bind_param("i", $search_order_id);
}
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Zbieranie danych z bazy do tablicy $grouped_orders
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

<form class="admin_search_container" method="GET" action="index.php">
    <input type="hidden" name="page" value="admin">
    <input type="hidden" name="subpage" value="orders_list">
    <input type="text" name="order_id" placeholder="Wyszukaj po numerze zamówienia" value="<?= htmlspecialchars($search_order_id, ENT_QUOTES, 'UTF-8') ?>"/>
    <button class="search_btn" type="submit">Szukaj</button>
</form>

<div class="orders">
    <?php if (empty($grouped_orders)): ?>
        <p>Brak zamówień.</p>
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
                               href="index.php?page=admin&subpage=order_details&order_id=<?= htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8') ?>">Szczegóły
                                zamówienia</a></h4>
                        <h4>Łącznie: <?= htmlspecialchars(number_format($order['total_price'], 2, ',', ' '), ENT_QUOTES, 'UTF-8') ?>zł</h4>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
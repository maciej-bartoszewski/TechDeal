<?php
session_start();
require '../../db_connect.php';
global $mysqli;

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error_message'] = 'Błędny CSRF token, spróbuj ponownie.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

$user_id = $_SESSION['user_id'];
$payment_id = $_POST['payment_id'] ?? null;
$total_price = $_POST['total_price'] ?? null;
$address_id = $_POST['address_id'] ?? null;

if (!$payment_id || !$address_id) {
    $_SESSION['error_message'] = 'Brak wystarczających informacji.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

// Pobranie informacji o adresie na podstawie przekazanego address_id z bazy
$stmt = $mysqli->prepare("SELECT country, city, post_code, street, building_number, apartment_number FROM addresses WHERE address_id = ?");
$stmt->bind_param("i", $address_id);
$stmt->execute();
$country = $city = $post_code = $street = $building_number = $apartment_number = null;
$stmt->bind_result($country, $city, $post_code, $street, $building_number, $apartment_number);
$stmt->fetch();
$stmt->close();

// Pobranie informacji o produktach które znjadują sie w koszyku użytkownika
$cart_stmt = $mysqli->prepare("SELECT product_id, quantity FROM cart_items WHERE cart_id = (SELECT cart_id FROM carts WHERE user_id = ?)");
$cart_stmt->bind_param("i", $user_id);
$cart_stmt->execute();
$cart_stmt->store_result();

$lacking_quantity = false;
$cart_items = [];

if ($cart_stmt->num_rows > 0) {
    $product_id = $quantity = $stock_quantity = null;
    $cart_stmt->bind_result($product_id, $quantity);
    // Sprawdzenie czy w bazie jest wystarczająca ilość produktów aby złożyć zamówienie
    while ($cart_stmt->fetch()) {
        $product_stmt = $mysqli->prepare("SELECT stock_quantity FROM products WHERE product_id = ?");
        $product_stmt->bind_param("i", $product_id);
        $product_stmt->execute();
        $product_stmt->bind_result($stock_quantity);
        $product_stmt->fetch();
        $product_stmt->close();

        if ($quantity > $stock_quantity) {
            $lacking_quantity = true;
            break;
        }
        $cart_items[] = ['product_id' => $product_id, 'quantity' => $quantity, 'stock_quantity' => $stock_quantity];
    }
}

$cart_stmt->close();

// Brak wystarczającej ilości produktu w bazie
if ($lacking_quantity) {
    $_SESSION['error_message'] = 'Nie można złożyć zamówienia, ponieważ jedna lub więcej produktów ma zbyt małą ilość w magazynie.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

// Dodanie do bazy informacji o nowym zamówieniu
$order_date = date('Y-m-d H:i:s');
$order_stmt = $mysqli->prepare("INSERT INTO orders (user_id, payment_id, order_date, total_price, country, city, post_code, street, building_number, apartment_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$order_stmt->bind_param("iissssssis", $user_id, $payment_id, $order_date, $total_price, $country, $city, $post_code, $street, $building_number, $apartment_number);
$order_stmt->execute();
$order_id = $order_stmt->insert_id;
$order_stmt->close();

$order_item_stmt = $mysqli->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
$update_stock_stmt = $mysqli->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");

foreach ($cart_items as $item) {
    // Dodanie produktu do bazy jako złożone zamówienie
    $order_item_stmt->bind_param("iii", $order_id, $item['product_id'], $item['quantity']);
    $order_item_stmt->execute();

    // Zaktualizowanie danych o ilości produktu w bazie
    $update_stock_stmt->bind_param("ii", $item['quantity'], $item['product_id']);
    $update_stock_stmt->execute();
}

$order_item_stmt->close();
$update_stock_stmt->close();

// Usunięcie danych o produktach przechowywanych w koszyku w bazie
$delete_cart_items_stmt = $mysqli->prepare("DELETE FROM cart_items WHERE cart_id = (SELECT cart_id FROM carts WHERE user_id = ?)");
$delete_cart_items_stmt->bind_param("i", $user_id);
$delete_cart_items_stmt->execute();
$delete_cart_items_stmt->close();

header("Location: ../../index.php?page=shopping_cart&subpage=confirmation");
exit();
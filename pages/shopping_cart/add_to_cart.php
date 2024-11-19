<?php
session_start();
require '../../db_connect.php';
global $mysqli;

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error_message'] = 'Błędny CSRF token, spróbuj ponownie.';
    echo '<script>window.history.go(-1);</script>';
    exit;
}

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = 1;

if (isset($_SESSION['user_id'])) {
    // Użytkownik zalogowany, dodaj produkt do bazy danych
    $user_id = $_SESSION['user_id'];

    // Pobranie cart_id użytkownika
    $cart_query = $mysqli->prepare("SELECT cart_id FROM carts WHERE user_id = ? LIMIT 1");
    $cart_query->bind_param("i", $user_id);
    $cart_query->execute();
    $cart_result = $cart_query->get_result()->fetch_assoc();
    $cart_id = $cart_result['cart_id'];

    // Sprawdzenie, czy produkt jest w koszyku
    $check_item_query = $mysqli->prepare("SELECT quantity FROM cart_items WHERE cart_id = ? AND product_id = ?");
    $check_item_query->bind_param("ii", $cart_id, $product_id);
    $check_item_query->execute();
    $check_item_result = $check_item_query->get_result();

    if ($check_item_result->num_rows > 0) {
        // Zaktualizuj ilość produktu w koszyku
        $update_quantity_query = $mysqli->prepare("UPDATE cart_items SET quantity = quantity + ? WHERE cart_id = ? AND product_id = ?");
        $update_quantity_query->bind_param("iii", $quantity, $cart_id, $product_id);
        if ($update_quantity_query->execute()) {
            $_SESSION['info_message'] = 'Produkt został pomyślnie dodany do koszyka.';
        } else {
            $_SESSION['error_message'] = 'Wystąpił błąd podczas dodawania produktu do koszyka';
        }
    } else {
        // Dodaj produkt do koszyka
        $insert_item_query = $mysqli->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert_item_query->bind_param("iii", $cart_id, $product_id, $quantity);
        if ($insert_item_query->execute()) {
            $_SESSION['info_message'] = 'Produkt został pomyślnie dodany do koszyka.';
        } else {
            $_SESSION['error_message'] = 'Wystąpił błąd podczas dodawania produktu do koszyka.';
        }
    }
} else {
    // Użytkownik niezalogowany, dodaj do koszyka w sesji
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = [
            'product_id' => $product_id,
            'quantity' => $quantity
        ];
    }
    $_SESSION['info_message'] = 'Produkt został pomyślnie dodany do koszyka.';
}

echo '<script>window.history.go(-1);</script>';
exit;
?>
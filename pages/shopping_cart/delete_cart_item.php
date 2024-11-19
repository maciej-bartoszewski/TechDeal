<?php
session_start();
require '../../db_connect.php';
global $mysqli;

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error_message'] = 'Błędny CSRF token, spróbuj ponownie.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

if (isset($_POST['product_id'])) {
    $itemId = (int)$_POST['product_id'];

    // Usunięcie produktu z bazy danych - użytkownik zalogowany
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $stmt = $mysqli->prepare("DELETE FROM cart_items WHERE product_id = ? AND cart_id = (SELECT cart_id FROM carts WHERE user_id = ?)");
        $stmt->bind_param('ii', $itemId, $user_id);
        $stmt->execute();
    }
    // Usunięcie produktu z sesji - użytkownik niezalogowany
    else {
        if (isset($_SESSION['cart'][$itemId])) {
            unset($_SESSION['cart'][$itemId]);
        }
    }
    $_SESSION['info_message'] = 'Usunięto produkt z koszyka.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
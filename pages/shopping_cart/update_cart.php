<?php
session_start();
require '../../db_connect.php';
global $mysqli;

if (isset($_POST['quantity_decrease']) || isset($_POST['quantity_increase'])) {
    $productId = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // Pobranie informacji o ilości produktu w bazie
    $product_query = "SELECT stock_quantity FROM products WHERE product_id = ?";
    $stmt = $mysqli->prepare($product_query);
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $product_result = $stmt->get_result();
    $product = $product_result->fetch_assoc();
    $maxQuantity = $product['stock_quantity'];

    $quantity_change = true;
    $quantity_max_error = false;
    if (isset($_POST['quantity_decrease'])) {
        $quantity -= 1;
    }
    if (isset($_POST['quantity_increase'])) {
        $quantity += 1;
    }
    if ($quantity > $maxQuantity) {
        $quantity = $maxQuantity;
        $quantity_change = false;
        $quantity_max_error = true;
    }

    if ($quantity <= 0) {
        // Usuń produkt z koszyka
        header("Location: delete_cart_item.php?id=$productId");
        exit();
    } else {
        // Aktualizacja ilości produktu
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $cart_query = $mysqli->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
            $cart_query->bind_param("i", $user_id);
            $cart_query->execute();
            $cart_result = $cart_query->get_result();

            $cart_id = $cart_result->fetch_assoc()['cart_id'];
            $update_quantity_query = $mysqli->prepare("UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND product_id = ?");
            $update_quantity_query->bind_param("iii", $quantity, $cart_id, $productId);
            $update_quantity_query->execute();
        } else {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
        }
        if ($quantity_max_error) {
            $_SESSION['error_message'] = "Nie można dodać kolejnego produktu.";
        } else {
            $_SESSION['info_message'] = "Pomyślnie zaktualizowano koszyk.";
        }
    }
}


header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
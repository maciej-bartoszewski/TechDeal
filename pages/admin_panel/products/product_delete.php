<?php
session_start();
require '../../../db_connect.php';
global $mysqli;

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error_message'] = 'Błędny CSRF token, spróbuj ponownie.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

if (isset($_POST['product_id']) && is_numeric($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    $query = "DELETE FROM products WHERE product_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $product_id);

    try {
        $stmt->execute();
        $_SESSION['info_message'] = 'Usunięto produkt.';
    } catch (mysqli_sql_exception $e) {
        $_SESSION['error_message'] = 'Nie można usunąć produktu, ponieważ jest on powiązany z zamówieniami.';
    }

    $stmt->close();
    echo '<script>window.history.go(-1);</script>';
    exit;
}
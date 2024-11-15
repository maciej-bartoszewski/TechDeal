<?php
session_start();
require '../../../db_connect.php';
global $mysqli;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];

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
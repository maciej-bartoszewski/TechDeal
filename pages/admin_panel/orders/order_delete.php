<?php
session_start();
require '../../../db_connect.php';
global $mysqli;

if (isset($_GET['order_id']) && is_numeric($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    try {
        // Usuwanie produktów z zamówienia
        $query = "DELETE FROM order_items WHERE order_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $stmt->close();

        // Usuwanie zamówienia
        $query = "DELETE FROM orders WHERE order_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['info_message'] = 'Zamówienie usunięte pomyślnie.';
    } catch (mysqli_sql_exception $e) {
        $_SESSION['error_message'] = 'Nie udało się usunąć zamówienia.';
    }

    $referrer = $_SERVER['HTTP_REFERER'] ?? '';
    if (strpos($referrer, 'order_details') !== false) {
        // Usuwamy zamówienie z podstrony z detalami zamówienia, bo strpos znalazlo 'order_details' w referrerze
        echo '<script>window.history.go(-2);</script>';
    } else {
        echo '<script>window.history.go(-1);</script>';
    }
    exit;
}
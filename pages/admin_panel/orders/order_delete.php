<?php
session_start();
require '../../../db_connect.php';
global $mysqli;

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error_message'] = 'Błędny CSRF token, spróbuj ponownie.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

if (isset($_POST['order_id']) && is_numeric($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

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
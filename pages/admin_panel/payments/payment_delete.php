<?php
session_start();
require '../../../db_connect.php';
global $mysqli;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $payment_id = $_GET['id'];

    $query = "DELETE FROM payments WHERE payment_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $payment_id);

    try {
        $stmt->execute();
        $_SESSION['info_message'] = 'Usunięto metodę płatności.';
    } catch (mysqli_sql_exception $e) {
        $_SESSION['error_message'] = 'Nie można usunąć metody płatności, ponieważ jest on powiązana z zamówieniami.';
    }

    $stmt->close();
    echo '<script>window.history.go(-1);</script>';
    exit;
}
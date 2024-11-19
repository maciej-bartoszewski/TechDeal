<?php
session_start();
require '../../../db_connect.php';
global $mysqli;

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error_message'] = 'Błędny CSRF token, spróbuj ponownie.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

if (isset($_POST['payment_id']) && is_numeric($_POST['payment_id'])) {
    $payment_id = $_POST['payment_id'];

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
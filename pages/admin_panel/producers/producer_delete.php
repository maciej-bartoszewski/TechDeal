<?php
session_start();
require '../../../db_connect.php';
global $mysqli;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $producer_id = $_GET['id'];

    $query = "DELETE FROM producers WHERE producer_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $producer_id);

    try {
        $stmt->execute();
        $_SESSION['info_message'] = 'Usunięto producenta.';
    } catch (mysqli_sql_exception $e) {
        $_SESSION['error_message'] = 'Nie można usunąć producenta, ponieważ jest on powiązany z zamówieniami.';
    }

    $stmt->close();
    echo '<script>window.history.go(-1);</script>';
    exit;
}
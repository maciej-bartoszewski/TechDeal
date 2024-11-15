<?php
session_start();
require '../../../db_connect.php';
global $mysqli;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $category_id = $_GET['id'];

    $query = "DELETE FROM categories WHERE category_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $category_id);

    try {
        $stmt->execute();
        $_SESSION['info_message'] = 'Usunięto kategorię.';
    } catch (mysqli_sql_exception $e) {
        $_SESSION['error_message'] = 'Nie można usunąć kategorii, ponieważ jest ona powiązana z zamówieniami.';
    }

    $stmt->close();
    echo '<script>window.history.go(-1);</script>';
    exit;
}
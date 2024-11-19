<?php
session_start();
require '../../../db_connect.php';
global $mysqli;

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error_message'] = 'Błędny CSRF token, spróbuj ponownie.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

if (isset($_POST['category_id']) && is_numeric($_POST['category_id'])) {
    $category_id = $_POST['category_id'];

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
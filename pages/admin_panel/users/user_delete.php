<?php
session_start();
require '../../../db_connect.php';
global $mysqli;

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error_message'] = 'Błędny CSRF token, spróbuj ponownie.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

if (isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    try {
        $query_user = "DELETE FROM users WHERE user_id = ?";
        $stmt_user = $mysqli->prepare($query_user);
        $stmt_user->bind_param("i", $user_id);
        $stmt_user->execute();
        $stmt_user->close();

        $_SESSION['info_message'] = 'Usunięto użytkownika jego koszyk i zamówienia.';
    } catch (mysqli_sql_exception $e) {
        $_SESSION['error_message'] = 'Nie można usunąć użytkownika.';
    }

    echo '<script>window.history.go(-1);</script>';
    exit;
}
echo 'error';
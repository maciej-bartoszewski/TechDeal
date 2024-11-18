<?php
session_start();
require '../../../db_connect.php';
global $mysqli;

if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

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
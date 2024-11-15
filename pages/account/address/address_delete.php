<?php
session_start();
require '../../../db_connect.php';
global $mysqli;

$user_id = $_SESSION['user_id'];

if (isset($_GET['address_id'])) {
    // Usunięcie adresu z bazy
    $address_id = $_GET['address_id'];
    $stmt = $mysqli->prepare("DELETE FROM addresses WHERE user_id = ? AND address_id = ?");
    $stmt->bind_param("ii", $user_id, $address_id);
    $stmt->execute();
    $stmt->close();
    $_SESSION['info_message'] = 'Usunięto adres.';
    echo '<script>window.history.go(-1);</script>';
    exit();
} else {
    $_SESSION['error_message'] = "Nie podano identyfikatora adresu do usunięcia.";
}
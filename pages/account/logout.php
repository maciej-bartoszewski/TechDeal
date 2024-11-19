<?php
session_start();
// Usunięcie danych w sesji
unset($_SESSION['user_id']);
unset($_SESSION['is_admin']);
unset($_SESSION['csrf_token']);
unset($_SESSION['cart']);

$_SESSION['info_message'] = 'Wylogowano pomyślnie.';
header('Location: http://localhost/techdeal/index.php?page=login');
exit;
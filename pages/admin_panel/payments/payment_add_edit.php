<?php
require 'db_connect.php';
global $mysqli;

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$payment_method = $image_path = '';
$mode = $_GET['subpage'] ?? null;
$payment_id = $_GET['payment_id'] ?? null;

// Pobranie informacji o metodzie platnosci jesli tryb edycji
if ($mode == 'payment_edit' && $payment_id) {
    $stmt = $mysqli->prepare("SELECT payment_method, image_path FROM payments WHERE payment_id = ?");
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $stmt->bind_result($payment_method, $image_path);
    $stmt->fetch();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = 'Błędny CSRF token, spróbuj ponownie.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $payment_method = trim($_POST['payment_method']);
    $image_path = trim($_POST['image_path']);

    if (empty($payment_method)) {
        $errors['payment_method'] = 'Metoda płatności jest wymagana.';
    }
    if (empty($image_path)) {
        $errors['image_path'] = 'Ścieżka do obrazu jest wymagana.';
    }

    if (empty($errors)) {
        if ($mode == 'payment_edit' && $payment_id) {
            // Altualizowanie metody płatności
            $stmt = $mysqli->prepare("UPDATE payments SET payment_method = ?, image_path = ? WHERE payment_id = ?");
            $stmt->bind_param("ssi", $payment_method, $image_path, $payment_id);
            $_SESSION['info_message'] = 'Zaktualizowano metodę płatności.';
        } else {
            // Dodanie nowej metody płatności
            $stmt = $mysqli->prepare("INSERT INTO payments (payment_method, image_path) VALUES (?, ?)");
            $stmt->bind_param("ss", $payment_method, $image_path);
            $_SESSION['info_message'] = 'Dodano nową metodę płatności.';
        }
        $stmt->execute();
        $stmt->close();

        echo '<script>window.history.go(-2);</script>';
        exit();
    }
}
?>

<h3><?= $mode == 'payment_edit' ? 'Edytuj metodę płatności' : 'Dodaj nową metodę płatności' ?></h3>
<form action="" method="POST" onsubmit="return validatePaymentAddEdit()">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
    <div class="form_group">
        <label for="payment_method">Metoda płatności</label>
        <input type="text" id="payment_method" name="payment_method" value="<?= htmlspecialchars($payment_method, ENT_QUOTES, 'UTF-8') ?>"/>
        <span class="error"><?= htmlspecialchars($errors['payment_method'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <div class="form_group">
        <label for="image_path">Ścieżka do obrazu</label>
        <input type="text" id="image_path" name="image_path" value="<?= htmlspecialchars($image_path, ENT_QUOTES, 'UTF-8') ?>" oninput="updateImagePreview()"/>
        <span class="error"><?= htmlspecialchars($errors['image_path'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <div class="form_group">
        <img id="image_preview" src="<?= htmlspecialchars($image_path, ENT_QUOTES, 'UTF-8') ?>" alt="Podgląd obrazu"/>
    </div>

    <button type="submit" class="red_button"><?= $mode == 'payment_edit' ? 'Zaktualizuj' : 'Dodaj' ?></button>
</form>
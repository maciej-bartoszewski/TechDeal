<?php
require 'db_connect.php';
global $mysqli;

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$user_id = $_SESSION['user_id'];
$current_password = $new_password = $repeated_new_password = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = 'Błędny CSRF token, spróbuj ponownie.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $repeated_new_password = trim($_POST['repeated_new_password']);

    $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';

    // Pobranie bieżącego hasła użytkownika
    $stmt = $mysqli->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $hashed_password = null;
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($current_password, $hashed_password)) {
        $errors['current_password'] = 'Aktualne hasło jest niepoprawne.';
    }

    if (empty($new_password)) {
        $errors['new_password'] = 'Nowe hasło jest wymagane.';
    } elseif (!preg_match($passwordPattern, $new_password)) {
        $errors['new_password'] = 'Hasło musi mieć co najmniej 8 znaków, jedną małą literę, jedną dużą literę, jedną cyfrę i jeden znak specjalny.';
    }

    if (empty($repeated_new_password)) {
        $errors['repeated_new_password'] = 'Powtórz nowe hasło.';
    } elseif ($new_password !== $repeated_new_password) {
        $errors['repeated_new_password'] = 'Nowe hasła muszą być zgodne.';
    }

    if (empty($errors)) {
        // Aktualizacja hasła w bazie na nowe
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->bind_param("si", $new_hashed_password, $user_id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['info_message'] = 'Zaktualizowano hasło.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
?>

<h3>Zmień hasło</h3>
<form action="" onsubmit="return validatePasswordChange()" method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
    <div class="form_group">
        <label for="current_password">Aktualne hasło</label>
        <input type="password" id="current_password" name="current_password"/>
        <span class="error"><?= htmlspecialchars($errors['current_password'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <div class="form_group">
        <label for="new_password">Nowe hasło</label>
        <input type="password" id="new_password" name="new_password"/>
        <span class="error"><?= htmlspecialchars($errors['new_password'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <div class="form_group">
        <label for="repeated_new_password">Powtórz hasło</label>
        <input type="password" id="repeated_new_password" name="repeated_new_password"/>
        <span class="error"><?= htmlspecialchars($errors['repeated_new_password'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <button type="submit" class="red_button">Zaktualizuj hasło</button>
</form>
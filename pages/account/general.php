<?php
require 'db_connect.php';
global $mysqli;

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user_id = $_SESSION['user_id'];
$first_name = $last_name = $phone_number = $updated_email = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = 'Błędny CSRF token, spróbuj ponownie.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone_number = trim($_POST['phone_number']);
    $updated_email = trim($_POST['e_mail']);

    $namePattern = '/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+$/';
    $emailPattern = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
    $phonePattern = '/^\d{9}$/';

    // Walidacja
    if (empty($first_name)) {
        $errors['first_name'] = 'Imię jest wymagane.';
    } elseif (!preg_match($namePattern, $first_name)) {
        $errors['first_name'] = 'Imię może zawierać tylko litery.';
    }

    if (empty($last_name)) {
        $errors['last_name'] = 'Nazwisko jest wymagane.';
    } elseif (!preg_match($namePattern, $last_name)) {
        $errors['last_name'] = 'Nazwisko może zawierać tylko litery.';
    }

    if (empty($updated_email) || !preg_match($emailPattern, $updated_email)) {
        $errors['email'] = 'Niepoprawny adres e-mail.';
    } else {
        // Sprawdzenie czy nie ma już użytkownika na podany adres e-mail
        $stmt_check_email = $mysqli->prepare("SELECT email FROM users WHERE email = ? AND user_id != ?");
        $stmt_check_email->bind_param("si", $updated_email, $user_id);
        $stmt_check_email->execute();
        $stmt_check_email->store_result();
        if ($stmt_check_email->num_rows != 0) {
            $errors['email'] = 'Podany adres e-mail jest już zajęty. Użyj innego adresu.';
        }
        $stmt_check_email->close();
    }

    if (empty($phone_number)) {
        $errors['phone_number'] = 'Numer telefonu jest wymagany.';
    } elseif (!preg_match($phonePattern, $phone_number)) {
        $errors['phone_number'] = 'Numer telefonu musi mieć 9 cyfr.';
    }

    if (empty($errors)) {
        // Aktualizowanie danych
        $stmt = $mysqli->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone_number = ? WHERE user_id = ?");
        $stmt->bind_param("ssssi", $first_name, $last_name, $updated_email, $phone_number, $user_id);

        if ($stmt->execute()) {
            $_SESSION['info_message'] = 'Zaktualizowano dane konta.';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            echo "<p class='error'>Błąd podczas aktualizacji danych</p>";
        }
        $stmt->close();
    }
}

// Pobranie informacji o użytkowniku
$stmt = $mysqli->prepare("SELECT first_name, last_name, email, phone_number FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$db_email = null;
$stmt->bind_result($first_name, $last_name, $db_email, $phone_number);
$stmt->fetch();
$stmt->close();
?>

<h3>Dane konta</h3>
<form action="index.php?page=account&subpage=general" method="POST" onsubmit="return validateAccountUpdate()">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
    <div class="form_group">
        <label for="first_name">Imię</label>
        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($first_name, ENT_QUOTES, 'UTF-8') ?>" />
        <span class="error"><?= htmlspecialchars($errors['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <div class="form_group">
        <label for="last_name">Nazwisko</label>
        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($last_name, ENT_QUOTES, 'UTF-8') ?>" />
        <span class="error"><?= htmlspecialchars($errors['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <div class="form_group">
        <label for="e_mail">E-mail</label>
        <input type="email" id="e_mail" name="e_mail" value="<?= htmlspecialchars($db_email, ENT_QUOTES, 'UTF-8') ?>" />
        <span class="error"><?= htmlspecialchars($errors['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <div class="form_group">
        <label for="phone_number">Numer telefonu</label>
        <input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($phone_number, ENT_QUOTES, 'UTF-8') ?>" />
        <span class="error"><?= htmlspecialchars($errors['phone_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <button type="submit" class="red_button">Zaktualizuj konto</button>
</form>
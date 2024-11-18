<?php
require 'db_connect.php';
global $mysqli;

$errors = [];
$first_name = $last_name = $email = $phone_number = $password = $is_admin = '';
$user_id = $_GET['user_id'] ?? null;

if ($user_id) {
    $stmt = $mysqli->prepare("SELECT first_name, last_name, email, phone_number, is_admin FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($first_name, $last_name, $email, $phone_number, $is_admin);
    $stmt->fetch();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $password = $_POST['password'];
    $repeated_password = $_POST['repeated_password'];
    $is_admin = $_POST['account_type'] == '1' ? 1 : 0;

    $namePattern = '/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+$/';
    $emailPattern = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
    $phonePattern = '/^\d{9}$/';
    $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';

    // Validation
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

    if (empty($email)) {
        $errors['email'] = 'Adres e-mail jest wymagany.';
    } elseif (!preg_match($emailPattern, $email)) {
        $errors['email'] = 'Niepoprawny adres e-mail.';
    } else {
        // Check if email already exists
        $stmt_check_email = $mysqli->prepare("SELECT email FROM users WHERE email = ? AND user_id != ?");
        $stmt_check_email->bind_param("si", $email, $user_id);
        $stmt_check_email->execute();
        $stmt_check_email->store_result();

        if ($stmt_check_email->num_rows != 0) {
            $errors['email'] = 'Konto na podany adres e-mail już istnieje.';
        }

        $stmt_check_email->close();
    }

    if (empty($phone_number)) {
        $errors['phone_number'] = 'Numer telefonu jest wymagany.';
    } elseif (!preg_match($phonePattern, $phone_number)) {
        $errors['phone_number'] = 'Numer telefonu musi mieć 9 cyfr.';
    }

    if (!empty($password) && !preg_match($passwordPattern, $password)) {
        $errors['password'] = 'Hasło musi mieć co najmniej 8 znaków, jedną dużą literę, jedną małą literę, jedną cyfrę i jeden znak specjalny.';
    }

    if ($password !== $repeated_password) {
        $errors['repeated_password'] = 'Hasła muszą być identyczne.';
    }

    if (empty($errors)) {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone_number = ?, password = ?, is_admin = ? WHERE user_id = ?");
            $stmt->bind_param("ssssssi", $first_name, $last_name, $email, $phone_number, $hashed_password, $is_admin, $user_id);
        } else {
            $stmt = $mysqli->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone_number = ?, is_admin = ? WHERE user_id = ?");
            $stmt->bind_param("sssisi", $first_name, $last_name, $email, $phone_number, $is_admin, $user_id);
        }
        $stmt->execute();
        $stmt->close();

        $_SESSION['info_message'] = 'Zaktualizowano użytkownika.';
        echo '<script>window.history.go(-2);</script>';
        exit();
    }
}
?>

<h3>Edytuj użytkownika</h3>
<form action="" method="POST">
    <div class="form_group">
        <label for="first_name">Imię</label>
        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($first_name, ENT_QUOTES, 'UTF-8') ?>"/>
        <span class="error"><?= htmlspecialchars($errors['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <div class="form_group">
        <label for="last_name">Nazwisko</label>
        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($last_name, ENT_QUOTES, 'UTF-8') ?>"/>
        <span class="error"><?= htmlspecialchars($errors['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <div class="form_group">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"/>
        <span class="error"><?= htmlspecialchars($errors['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <div class="form_group">
        <label for="phone_number">Numer telefonu</label>
        <input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($phone_number, ENT_QUOTES, 'UTF-8') ?>"/>
        <span class="error"><?= htmlspecialchars($errors['phone_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <div class="form_group">
        <label for="password">Hasło</label>
        <input type="password" id="password" name="password"/>
        <span class="error"><?= htmlspecialchars($errors['password'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <div class="form_group">
        <label for="repeated_password">Powtórz hasło</label>
        <input type="password" id="repeated_password" name="repeated_password"/>
        <span class="error"><?= htmlspecialchars($errors['repeated_password'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <div class="form_group">
        <label for="account_type">Typ konta</label>
        <select id="account_type" name="account_type">
            <option value="0" <?= $is_admin == 0 ? 'selected' : '' ?>>Użytkownik</option>
            <option value="1" <?= $is_admin == 1 ? 'selected' : '' ?>>Administrator</option>
        </select>
    </div>
    <button type="submit" class="red_button">Zaktualizuj</button>
</form>
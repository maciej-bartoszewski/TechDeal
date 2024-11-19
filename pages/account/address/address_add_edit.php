<?php
require 'db_connect.php';
global $mysqli;

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$country = $street = $building_number = $apartment_number = $post_code = $city = '';
$user_id = $_SESSION['user_id'];
$address_id = $_GET['address_id'] ?? null;
$mode = $_GET['subpage'];

// Jesli tryb edycji adresu
if ($mode == 'address_edit' && $address_id) {
    // Pobranie informacji o adresie
    $stmt = $mysqli->prepare("SELECT country, street, building_number, apartment_number, post_code, city FROM addresses WHERE address_id = ?");
    $stmt->bind_param("i", $address_id);
    $stmt->execute();
    $stmt->bind_result($country, $street, $building_number, $apartment_number, $post_code, $city);
    $stmt->fetch();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = 'Błędny CSRF token, spróbuj ponownie.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $country = trim($_POST['country']);
    $street = trim($_POST['street']);
    $building_number = $_POST['building_number'];
    $apartment_number = $_POST['apartment_number'] ?: NULL;
    $post_code = trim($_POST['post_code']);
    $city = trim($_POST['city']);

    $namePattern = '/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+$/';

    // Walidacja
    if (empty($country)) {
        $errors['country'] = 'Kraj jest wymagany.';
    } elseif (!preg_match($namePattern, $country)) {
        $errors['country'] = 'Kraj może zawierać tylko litery.';
    }
    if (empty($street)) {
        $errors['street'] = 'Ulica jest wymagana.';
    }
    if (empty($building_number)) {
        $errors['building_number'] = 'Numer budynku jest wymagany.';
    } elseif (!is_numeric($building_number) || $building_number < 1) {
        $errors['building_number'] = 'Numer budynku musi być liczbą.';
    }
    if (!is_null($apartment_number) && (!is_numeric($apartment_number) || $apartment_number < 1)) {
        $errors['apartment_number'] = 'Numer mieszkania musi być liczbą.';
    }
    if (empty($post_code)) {
        $errors['post_code'] = 'Kod pocztowy jest wymagany.';
    } elseif (!preg_match('/^\d{2}-\d{3}$/', $post_code)) {
        $errors['post_code'] = 'Kod pocztowy musi być w formacie XX-XXX.';
    }
    if (empty($city)) {
        $errors['city'] = 'Miasto jest wymagane.';
    } elseif (!preg_match($namePattern, $city)) {
        $errors['city'] = 'Miasto może zawierać tylko litery.';
    }

    if (empty($errors)) {
        if ($mode == 'address_edit' && $address_id) {
            // Zaktualizowanie adresu
            $stmt = $mysqli->prepare("UPDATE addresses SET country = ?, street = ?, building_number = ?, apartment_number = ?, post_code = ?, city = ? WHERE address_id = ?");
            $stmt->bind_param("ssisssi", $country, $street, $building_number, $apartment_number, $post_code, $city, $address_id);
            $_SESSION['info_message'] = 'Zaktualizowano adres.';
        } else {
            // Dodanie nowego adresu
            $stmt = $mysqli->prepare("INSERT INTO addresses (user_id, country, street, building_number, apartment_number, post_code, city) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ississs", $user_id, $country, $street, $building_number, $apartment_number, $post_code, $city);
            $_SESSION['info_message'] = 'Dodano adres.';
        }
        $stmt->execute();
        $stmt->close();

        echo '<script>window.history.go(-2);</script>';
        exit();
    }
}
?>

<h3><?= $mode == 'address_edit' ? 'Edytuj adres' : 'Dodaj nowy adres' ?></h3>
<form action="" method="POST" onsubmit="return validateAddress()">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
    <div class="form_group">
        <label for="country">Kraj</label>
        <input type="text" id="country" name="country" value="<?= htmlspecialchars($country, ENT_QUOTES, 'UTF-8') ?>"/>
        <span class="error"><?= htmlspecialchars($errors['country'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <div class="form_group">
        <label for="street">Ulica</label>
        <input type="text" id="street" name="street" value="<?= htmlspecialchars($street, ENT_QUOTES, 'UTF-8') ?>"/>
        <span class="error"><?= htmlspecialchars($errors['street'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <div class="form_group">
        <label for="building_number">Numer budynku</label>
        <input type="number" id="building_number" name="building_number" value="<?= htmlspecialchars($building_number, ENT_QUOTES, 'UTF-8') ?>"/>
        <span class="error"><?= htmlspecialchars($errors['building_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <div class="form_group">
        <label for="apartment_number">Numer mieszkania</label>
        <input type="number" id="apartment_number" name="apartment_number" value="<?= htmlspecialchars($apartment_number, ENT_QUOTES, 'UTF-8') ?>"/>
        <span class="error"><?= htmlspecialchars($errors['apartment_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <div class="form_group">
        <label for="post_code">Kod pocztowy</label>
        <input type="text" id="post_code" name="post_code" value="<?= htmlspecialchars($post_code, ENT_QUOTES, 'UTF-8') ?>"/>
        <span class="error"><?= htmlspecialchars($errors['post_code'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <div class="form_group">
        <label for="city">Miasto</label>
        <input type="text" id="city" name="city" value="<?= htmlspecialchars($city, ENT_QUOTES, 'UTF-8') ?>"/>
        <span class="error"><?= htmlspecialchars($errors['city'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <button type="submit" class="red_button"><?= $mode == 'address_edit' ? 'Zaktualizuj adres' : 'Dodaj adres' ?></button>
</form>
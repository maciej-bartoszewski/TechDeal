<?php
require 'db_connect.php';
global $mysqli;

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user_id = $_SESSION['user_id'];

// Pobranie informacji o adresach użytkownika
$stmt = $mysqli->prepare("SELECT address_id, country, street, building_number, apartment_number, post_code, city FROM addresses WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$address_id = $country = $street = $building_number = $apartment_number = $postal_code = $city = null;
$stmt->bind_result($address_id, $country, $street, $building_number, $apartment_number, $postal_code, $city);
?>
<h3>Adresy</h3>
<div class="addresses">
    <div class="new_address">
        <a href="index.php?page=account&subpage=address_add">
            <img src="assets/icons/add.png" alt="Ikonka dodawania"/>Dodaj
        </a>
    </div>

    <?php while ($stmt->fetch()): ?>
        <div class="address">
            <div class="signle_info">
                <h4>Kraj:</h4>
                <p><?= htmlspecialchars($country, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div class="signle_info">
                <h4>Ulica:</h4>
                <p><?= htmlspecialchars($street, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div class="signle_info">
                <h4>Numer budynku:</h4>
                <p><?= htmlspecialchars($building_number, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div class="signle_info">
                <h4>Numer mieszkania:</h4>
                <p><?= htmlspecialchars($apartment_number ?: '-', ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div class="signle_info">
                <h4>Kod pocztowy:</h4>
                <p><?= htmlspecialchars($postal_code, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div class="signle_info">
                <h4>Miasto:</h4>
                <p><?= htmlspecialchars($city, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div class="actions">
                <div class="action">
                    <a href="index.php?page=account&subpage=address_edit&user_id=<?= htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8') ?>&address_id=<?= htmlspecialchars($address_id, ENT_QUOTES, 'UTF-8') ?>">
                        <img src="assets/icons/edit.png" alt="Ikonka edycji"/>Edytuj
                    </a>
                </div>
                <div class="action">
                    <form method="POST" action="/techdeal/pages/account/address/address_delete.php">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="address_id" value="<?= htmlspecialchars($address_id, ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="abutton">
                            <img src="assets/icons/delete.png" alt="Ikonka usuwania"/>Usuń
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endwhile; ?>

    <?php
    $stmt->close();
    ?>
</div>
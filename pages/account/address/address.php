<?php
require 'db_connect.php';
global $mysqli;

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
                <p><?= htmlspecialchars($country) ?></p>
            </div>
            <div class="signle_info">
                <h4>Ulica:</h4>
                <p><?= htmlspecialchars($street) ?></p>
            </div>
            <div class="signle_info">
                <h4>Numer budynku:</h4>
                <p><?= htmlspecialchars($building_number) ?></p>
            </div>
            <div class="signle_info">
                <h4>Numer mieszkania:</h4>
                <p><?= htmlspecialchars($apartment_number ?: '-') ?></p>
            </div>
            <div class="signle_info">
                <h4>Kod pocztowy:</h4>
                <p><?= htmlspecialchars($postal_code) ?></p>
            </div>
            <div class="signle_info">
                <h4>Miasto:</h4>
                <p><?= htmlspecialchars($city) ?></p>
            </div>
            <div class="actions">
                <div class="action">
                    <a href="index.php?page=account&subpage=address_edit&user_id=<?= $user_id ?>&address_id=<?= $address_id ?>">
                        <img src="assets/icons/edit.png" alt="Ikonka edycji"/>Edytuj
                    </a>
                </div>
                <div class="action">
                    <a href="/techdeal/pages/account/address/address_delete.php?address_id=<?= $address_id ?>>">
                        <img src="assets/icons/delete.png" alt="Ikonka usuwania"/>Usuń
                    </a>
                </div>
            </div>
        </div>
    <?php endwhile; ?>

    <?php
    $stmt->close();
    ?>
</div>
<?php
require 'db_connect.php';
global $mysqli;

$errors = [];
$producer_name = $image_path = '';
$mode = $_GET['subpage'] ?? null;
$producer_id = $_GET['producer_id'] ?? null;

// Pobranie danych o producencie do edycji
if ($mode == 'producer_edit' && $producer_id) {
    $stmt = $mysqli->prepare("SELECT producer_name, image_path FROM producers WHERE producer_id = ?");
    $stmt->bind_param("i", $producer_id);
    $stmt->execute();
    $stmt->bind_result($producer_name, $image_path);
    $stmt->fetch();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $producer_name = trim($_POST['producer_name']);
    $image_path = trim($_POST['image_path']);

    if (empty($producer_name)) {
        $errors['producer_name'] = 'Nazwa producenta jest wymagana.';
    }
    if (empty($image_path)) {
        $errors['image_path'] = 'Ścieżka do obrazu jest wymagana.';
    }

    // Aktualizacja lub dodanie nowego producenta
    if (empty($errors)) {
        if ($mode == 'producer_edit' && $producer_id) {
            $stmt = $mysqli->prepare("UPDATE producers SET producer_name = ?, image_path = ? WHERE producer_id = ?");
            $stmt->bind_param("ssi", $producer_name, $image_path, $producer_id);
            $_SESSION['info_message'] = 'Zaktualizowano producenta.';
        } else {
            $stmt = $mysqli->prepare("INSERT INTO producers (producer_name, image_path) VALUES (?, ?)");
            $stmt->bind_param("ss", $producer_name, $image_path);
            $_SESSION['info_message'] = 'Dodano nowego producenta.';
        }
        $stmt->execute();
        $stmt->close();

        echo '<script>window.history.go(-2);</script>';
        exit();
    }
}
?>

<h3><?= $mode == 'producer_edit' ? 'Edytuj producenta' : 'Dodaj nowego producenta' ?></h3>
<form action="" method="POST">
    <div class="form_group">
        <label for="producer_name">Nazwa producenta</label>
        <input type="text" id="producer_name" name="producer_name" value="<?= htmlspecialchars($producer_name, ENT_QUOTES, 'UTF-8') ?>" required/>
        <span class="error"><?= htmlspecialchars($errors['producer_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <div class="form_group">
        <label for="image_path">Ścieżka do obrazu</label>
        <input type="text" id="image_path" name="image_path" value="<?= htmlspecialchars($image_path, ENT_QUOTES, 'UTF-8') ?>" required oninput="updateImagePreview()"/>
        <span class="error"><?= htmlspecialchars($errors['image_path'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <div class="form_group">
        <img id="image_preview" src="<?= htmlspecialchars($image_path, ENT_QUOTES, 'UTF-8') ?>" alt="Podgląd obrazu"/>
    </div>

    <button type="submit" class="red_button"><?= $mode == 'producer_edit' ? 'Zaktualizuj' : 'Dodaj' ?></button>
</form>
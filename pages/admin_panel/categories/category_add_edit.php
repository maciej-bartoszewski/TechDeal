<?php
require 'db_connect.php';
global $mysqli;

$errors = [];
$category_name = '';
$mode = $_GET['subpage'] ?? null;
$category_id = $_GET['category_id'] ?? null;


// Pobranie danych o kategorii do edycji
if ($mode == 'category_edit' && $category_id) {
    $stmt = $mysqli->prepare("SELECT category_name FROM categories WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $stmt->bind_result($category_name);
    $stmt->fetch();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = trim($_POST['category_name']);

    if (empty($category_name)) {
        $errors['category_name'] = 'Nazwa kategorii jest wymagana.';
    }

    // Aktualizacja lub dodanie nowej kategorii
    if (empty($errors)) {
        if ($mode == 'category_edit' && $category_id) {
            $stmt = $mysqli->prepare("UPDATE categories SET category_name = ? WHERE category_id = ?");
            $stmt->bind_param("si", $category_name, $category_id);
            $_SESSION['info_message'] = 'Zaktualizowano kategorię.';
        } else {
            $stmt = $mysqli->prepare("INSERT INTO categories (category_name) VALUES (?)");
            $stmt->bind_param("s", $category_name);
            $_SESSION['info_message'] = 'Dodano nową kategorię.';
        }
        $stmt->execute();
        $stmt->close();

        echo '<script>window.history.go(-2);</script>';
        exit();
    }
}
?>

<h3><?= $mode == 'category_edit' ? 'Edytuj kategorię' : 'Dodaj nową kategorię' ?></h3>
<form action="" method="POST">
    <div class="form_group">
        <label for="category_name">Nazwa kategorii</label>
        <input type="text" id="category_name" name="category_name" value="<?= htmlspecialchars($category_name) ?>" required/>
        <span class="error"><?= $errors['category_name'] ?? '' ?></span>
    </div>

    <button type="submit" class="red_button"><?= $mode == 'category_edit' ? 'Zaktualizuj' : 'Dodaj' ?></button>
</form>
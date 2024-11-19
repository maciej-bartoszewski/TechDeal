<?php
require 'db_connect.php';
global $mysqli;

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$product_name = $description = $specification = $price = $stock_quantity = $image_path = '';
$category_id = $producer_id = null;
$mode = $_GET['subpage'] ?? null;
$product_id = $_GET['product_id'] ?? null;

// Pobranie informacji o kategoriach
$categories = [];
$category_stmt = $mysqli->prepare("SELECT category_id, category_name FROM categories");
$category_stmt->execute();
$category_result = $category_stmt->get_result();
while ($row = $category_result->fetch_assoc()) {
    $categories[] = $row;
}
$category_stmt->close();

// Pobranie informacji o producentach
$producers = [];
$producer_stmt = $mysqli->prepare("SELECT producer_id, producer_name FROM producers");
$producer_stmt->execute();
$producer_result = $producer_stmt->get_result();
while ($row = $producer_result->fetch_assoc()) {
    $producers[] = $row;
}
$producer_stmt->close();

// Jeśli edytujemy produkt, pobieramy jego dane
if ($mode == 'product_edit' && $product_id) {
    $stmt = $mysqli->prepare("SELECT category_id, producer_id, product_name, description, specification, price, stock_quantity, image_path FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($category_id, $producer_id, $product_name, $description, $specification, $price, $stock_quantity, $image_path);
    $stmt->fetch();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = 'Błędny CSRF token, spróbuj ponownie.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $category_id = $_POST['category_id'];
    $producer_id = $_POST['producer_id'];
    $product_name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $specification = trim($_POST['specification']);
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $image_path = trim($_POST['image_path']);

    // Walidacja
    if (empty($category_id)) {
        $errors['category_id'] = 'Kategoria jest wymagana.';
    }
    if (empty($producer_id)) {
        $errors['producer_id'] = 'Producent jest wymagany.';
    }

    if (empty($product_name)) {
        $errors['product_name'] = 'Nazwa produktu jest wymagana.';
    }
    if (empty($description)) {
        $errors['description'] = 'Opis produktu jest wymagany.';
    }
    if (empty($specification)) {
        $errors['specification'] = 'Specyfikacja produktu jest wymagana.';
    }
    if (empty($price) || !is_numeric($price) || $price < 0) {
        $errors['price'] = 'Cena produktu jest wymagana i musi być liczbą.';
    }
    if (empty($stock_quantity) || !is_numeric($stock_quantity) || $stock_quantity < 0) {
        $errors['stock_quantity'] = 'Stan magazynowy jest wymagany i musi być liczbą.';
    }
    if (empty($image_path)) {
        $errors['image_path'] = 'Ścieżka do obrazu jest wymagana.';
    }

    if (empty($errors)) {
        // Jesli edytujemy produkt, aktualizujemy dane
        if ($mode == 'product_edit' && $product_id) {
            $stmt = $mysqli->prepare("UPDATE products SET category_id = ?, producer_id = ?, product_name = ?, description = ?, specification = ?, price = ?, stock_quantity = ?, image_path = ? WHERE product_id = ?");
            $stmt->bind_param("iisssdiss", $category_id, $producer_id, $product_name, $description, $specification, $price, $stock_quantity, $image_path, $product_id);
            $_SESSION['info_message'] = 'Zaktualizowano produkt.';
        } else {
            // W przeciwnym wypadku dodajemy nowy produkt
            $stmt = $mysqli->prepare("INSERT INTO products (category_id, producer_id, product_name, description, specification, price, stock_quantity, image_path, added_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("iisssdis", $category_id, $producer_id, $product_name, $description, $specification, $price, $stock_quantity, $image_path);
            $_SESSION['info_message'] = 'Dodano nowy produkt.';
        }
        $stmt->execute();
        $stmt->close();

        echo '<script>window.history.go(-2);</script>';
        exit();
    }
}
?>

<h3><?= $mode == 'product_edit' ? 'Edytuj produkt' : 'Dodaj nowy produkt' ?></h3>
<form action="" method="POST" onsubmit="return validateProductAddEdit()">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
    <div class="form_group">
        <label for="category_id">Kategoria</label>
        <select id="category_id" name="category_id">
            <option value="">Wybierz kategorię</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category['category_id'], ENT_QUOTES, 'UTF-8') ?>" <?= $category_id == $category['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category['category_name'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <span class="error"><?= htmlspecialchars($errors['category_id'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <div class="form_group">
        <label for="producer_id">Producent</label>
        <select id="producer_id" name="producer_id">
            <option value="">Wybierz producenta</option>
            <?php foreach ($producers as $producer): ?>
                <option value="<?= htmlspecialchars($producer['producer_id'], ENT_QUOTES, 'UTF-8') ?>" <?= $producer_id == $producer['producer_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($producer['producer_name'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <span class="error"><?= htmlspecialchars($errors['producer_id'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <div class="form_group">
        <label for="product_name">Nazwa produktu</label>
        <input type="text" id="product_name" name="product_name" value="<?= htmlspecialchars($product_name, ENT_QUOTES, 'UTF-8') ?>"/>
        <span class="error"><?= htmlspecialchars($errors['product_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <div class="form_group">
        <label for="price">Cena</label>
        <input type="text" id="price" name="price" value="<?= htmlspecialchars($price, ENT_QUOTES, 'UTF-8') ?>"/>
        <span class="error"><?= htmlspecialchars($errors['price'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <div class="form_group">
        <label for="stock_quantity">Stan magazynowy</label>
        <input type="number" id="stock_quantity" name="stock_quantity" value="<?= htmlspecialchars($stock_quantity, ENT_QUOTES, 'UTF-8') ?>"/>
        <span class="error"><?= htmlspecialchars($errors['stock_quantity'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <div class="form_group">
        <label for="image_path">Ścieżka do obrazu</label>
        <input type="text" id="image_path" name="image_path" value="<?= htmlspecialchars($image_path, ENT_QUOTES, 'UTF-8') ?>" oninput="updateImagePreview()"/>
        <span class="error"><?= htmlspecialchars($errors['image_path'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <div class="form_group">
        <img id="image_preview" src="<?= htmlspecialchars($image_path, ENT_QUOTES, 'UTF-8') ?>" alt="Podgląd obrazu"/>
    </div>
    <div class="form_group">
        <label for="description">Opis</label>
        <textarea id="description" name="description"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></textarea>
        <span class="error"><?= htmlspecialchars($errors['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <div class="form_group">
        <label for="specification">Specyfikacja</label>
        <textarea id="specification" name="specification"><?= htmlspecialchars($specification, ENT_QUOTES, 'UTF-8') ?></textarea>
        <span class="error"><?= htmlspecialchars($errors['specification'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <button type="submit" class="red_button"><?= $mode == 'product_edit' ? 'Zaktualizuj' : 'Dodaj' ?></button>
</form>
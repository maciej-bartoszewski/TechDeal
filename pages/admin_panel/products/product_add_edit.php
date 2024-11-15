<?php
require 'db_connect.php';
global $mysqli;

$errors = [];
$product_name = $description = $specification = $price = $stock_quantity = $image_path = '';
$category_id = $producer_id = null;
$mode = $_GET['subpage'] ?? null;
$product_id = $_GET['product_id'] ?? null;

$categories = [];
$category_stmt = $mysqli->prepare("SELECT category_id, category_name FROM categories");
$category_stmt->execute();
$category_result = $category_stmt->get_result();
while ($row = $category_result->fetch_assoc()) {
    $categories[] = $row;
}
$category_stmt->close();

$producers = [];
$producer_stmt = $mysqli->prepare("SELECT producer_id, producer_name FROM producers");
$producer_stmt->execute();
$producer_result = $producer_stmt->get_result();
while ($row = $producer_result->fetch_assoc()) {
    $producers[] = $row;
}
$producer_stmt->close();

if ($mode == 'product_edit' && $product_id) {
    $stmt = $mysqli->prepare("SELECT category_id, producer_id, product_name, description, specification, price, stock_quantity, image_path FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($category_id, $producer_id, $product_name, $description, $specification, $price, $stock_quantity, $image_path);
    $stmt->fetch();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_id = $_POST['category_id'];
    $producer_id = $_POST['producer_id'];
    $product_name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $specification = trim($_POST['specification']);
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $image_path = trim($_POST['image_path']);

    if (empty($product_name)) {
        $errors['product_name'] = 'Nazwa produktu jest wymagana.';
    }
    if (empty($description)) {
        $errors['description'] = 'Opis produktu jest wymagany.';
    }
    if (empty($specification)) {
        $errors['specification'] = 'Specyfikacja produktu jest wymagana.';
    }
    if (empty($price) || !is_numeric($price)) {
        $errors['price'] = 'Cena produktu jest wymagana i musi być liczbą.';
    }
    if (empty($stock_quantity) || !is_numeric($stock_quantity)) {
        $errors['stock_quantity'] = 'Stan magazynowy jest wymagany i musi być liczbą.';
    }
    if (empty($image_path)) {
        $errors['image_path'] = 'Ścieżka do obrazu jest wymagana.';
    }

    if (empty($errors)) {
        if ($mode == 'product_edit' && $product_id) {
            $stmt = $mysqli->prepare("UPDATE products SET category_id = ?, producer_id = ?, product_name = ?, description = ?, specification = ?, price = ?, stock_quantity = ?, image_path = ? WHERE product_id = ?");
            $stmt->bind_param("iisssdiss", $category_id, $producer_id, $product_name, $description, $specification, $price, $stock_quantity, $image_path, $product_id);
            $_SESSION['info_message'] = 'Zaktualizowano produkt.';
        } else {
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
<form action="" method="POST">
    <div class="form_group">
        <label for="category_id">Kategoria</label>
        <select id="category_id" name="category_id" class="select_in_product" required>
            <option value="">Wybierz kategorię</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category['category_id']) ?>" <?= $category_id == $category['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category['category_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form_group">
        <label for="producer_id">Producent</label>
        <select id="producer_id" name="producer_id" class="select_in_product" required>
            <option value="">Wybierz producenta</option>
            <?php foreach ($producers as $producer): ?>
                <option value="<?= htmlspecialchars($producer['producer_id']) ?>" <?= $producer_id == $producer['producer_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($producer['producer_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form_group">
        <label for="product_name">Nazwa produktu</label>
        <input type="text" id="product_name" name="product_name" value="<?= htmlspecialchars($product_name) ?>"/>
        <span class="error"><?= $errors['product_name'] ?? '' ?></span>
    </div>
    <div class="form_group">
        <label for="price">Cena</label>
        <input type="text" id="price" name="price" value="<?= htmlspecialchars($price) ?>"/>
        <span class="error"><?= $errors['price'] ?? '' ?></span>
    </div>
    <div class="form_group">
        <label for="stock_quantity">Stan magazynowy</label>
        <input type="number" id="stock_quantity" name="stock_quantity" value="<?= htmlspecialchars($stock_quantity) ?>"/>
        <span class="error"><?= $errors['stock_quantity'] ?? '' ?></span>
    </div>
    <div class="form_group">
        <label for="image_path">Ścieżka do obrazu</label>
        <input type="text" id="image_path" name="image_path" value="<?= htmlspecialchars($image_path) ?>" required oninput="updateImagePreview()"/>
        <span class="error"><?= $errors['image_path'] ?? '' ?></span>
    </div>
    <div class="form_group">
        <img id="image_preview" src="<?= htmlspecialchars($image_path) ?>" alt="Podgląd obrazu"/>
    </div>
    <div class="form_group">
        <label for="description">Opis</label>
        <textarea id="description" name="description"><?= htmlspecialchars($description) ?></textarea>
        <span class="error"><?= $errors['description'] ?? '' ?></span>
    </div>
    <div class="form_group">
        <label for="specification">Specyfikacja</label>
        <textarea id="specification" name="specification"><?= htmlspecialchars($specification) ?></textarea>
        <span class="error"><?= $errors['specification'] ?? '' ?></span>
    </div>
    <button type="submit" class="red_button"><?= $mode == 'product_edit' ? 'Zaktualizuj' : 'Dodaj' ?></button>
</form>
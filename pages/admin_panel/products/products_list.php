<?php
require 'db_connect.php';
global $mysqli;

$search_product = $_GET['search_product'] ?? '';

// Pobranie informacji o produktach z bazy danych
$query = "SELECT p.product_id, p.product_name, p.image_path, p.price, p.stock_quantity, p.added_at, c.category_name, pr.producer_name
          FROM products p
          JOIN categories c ON p.category_id = c.category_id
          JOIN producers pr ON p.producer_id = pr.producer_id";

// Jesli sie wyszukuje to zastosowanie filtru dla nazwy produktu, kategorii i producenta
if ($search_product) {
    $query .= " WHERE p.product_name LIKE ? OR
                c.category_name LIKE ? OR
                pr.producer_name LIKE ?";
}

// Sortowanie po dacie dodania
$query .= " ORDER BY p.added_at DESC";
$stmt = $mysqli->prepare($query);
if ($search_product) {
    $search_term = '%' . $search_product . '%';
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
}
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<div class="main_page_header">
    <h3>Produkty</h3>
    <a href="index.php?page=admin&subpage=product_add" class="add-btn">
        <img src="assets/icons/add.png" alt="Dodaj"/> Dodaj
    </a>
</div>

<form class="admin_search_container" method="GET" action="index.php">
    <input type="hidden" name="page" value="admin">
    <input type="hidden" name="subpage" value="products_list">
    <input type="text" name="search_product" placeholder="Wyszukaj produkt" value="<?= htmlspecialchars($search_product) ?>"/>
    <button class="search_btn" type="submit">Szukaj</button>
</form>

<div class="main_page_container">
    <?php if ($result->num_rows == 0): ?>
        <p>Brak produktów.</p>
    <?php else: ?>
        <?php while ($product = $result->fetch_assoc()): ?>
            <div class="product_container">
                <div class="product_info_img">
                    <div class="product_img">
                        <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="Obraz produktu"/>
                    </div>
                    <div class="product_info">
                        <p>Produkt: <strong><?= htmlspecialchars($product['product_name']) ?></strong></p>
                        <p>Kategoria: <strong><?= htmlspecialchars($product['category_name']) ?></strong></p>
                        <p>Producent: <strong><?= htmlspecialchars($product['producer_name']) ?></strong></p>
                        <p>Cena: <strong><?= htmlspecialchars($product['price']) ?> PLN</strong></p>
                        <p>Stan magazynowy: <strong><?= htmlspecialchars($product['stock_quantity']) ?></strong></p>
                    </div>
                </div>
                <div class="data_actions">
                    <a href="index.php?page=admin&subpage=product_edit&product_id=<?= htmlspecialchars($product['product_id']) ?>"
                       class="edit-btn">
                        <img src="assets/icons/edit.png" alt="Edytuj"/>Edytuj</a>
                    <a href="pages/admin_panel/products/product_delete.php?id=<?= htmlspecialchars($product['product_id']) ?>"
                       class="delete-btn">
                        <img src="assets/icons/delete.png" alt="Usuń"/> Usuń
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>
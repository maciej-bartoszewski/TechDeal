<?php
require 'db_connect.php';
global $mysqli;

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Pobranie informacji o produkcie z bazy
$product_query = "SELECT * FROM products WHERE product_id = ?";
$stmt = $mysqli->prepare($product_query);
$stmt->bind_param('i', $product_id);
$stmt->execute();
$product_result = $stmt->get_result();
$product = $product_result->fetch_assoc();
$stmt->close();

if ($product): ?>
    <div class="product_page" id="product_<?php echo htmlspecialchars($product['product_id'], ENT_QUOTES, 'UTF-8'); ?>">
        <h2><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></h2>
        <hr/>
        <img src="<?php echo htmlspecialchars($product['image_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="Zdjęcie Produktu"/>
        <hr/>
        <div class="product_price_info_container">
            <h3>
                Cena: <?php echo htmlspecialchars(number_format($product['price'], 2, ',', ' ') . ' zł', ENT_QUOTES, 'UTF-8'); ?></h3>
            <?php if ($product['stock_quantity'] != 0): ?>
                <form method="POST" action="pages/shopping_cart/add_to_cart.php">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id'], ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="product_link gray add-to-cart">Dodaj do koszyka</button>
                </form>
            <?php else: ?>
                <p class="product_unavailable">Produkt aktualnie niedostępny, skontaktuj się z nami.</p>
            <?php endif; ?>
        </div>
        <hr/>
        <div class="product_description">
            <h3>Opis produktu:</h3></br>
            <?php echo nl2br(htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8')); ?>
        </div>
        <hr/>
        <div class="product_specification">
            <h3>Specyfikacja produktu:</h3></br>
            <?php echo nl2br(htmlspecialchars($product['specification'], ENT_QUOTES, 'UTF-8')); ?>
        </div>
        <hr/>
    </div>
<?php else: ?>
    <p>Produkt nie został znaleziony.</p>
<?php endif; ?>
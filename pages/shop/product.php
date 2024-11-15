<?php
require 'db_connect.php';
global $mysqli;

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
    <div class="product_page" id="product_<?php echo $product['product_id']; ?>">
        <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
        <hr/>
        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="Zdjęcie Produktu"/>
        <hr/>
        <div class="product_price_info_container">
            <h3>Cena: <?php echo number_format($product['price'], 2, ',', ' ') . ' zł'; ?></h3>
            <?php if ($product['stock_quantity'] != 0): ?>
                <a href="pages/shopping_cart/add_to_cart.php?id=<?php echo $product['product_id']; ?>#product_<?php echo $product['product_id']; ?>"
                   class="product_link gray">Dodaj do koszyka</a>
            <?php else: ?>
                <p class="product_unavailable">Produkt aktualnie niedostępny, skontaktuj się z nami.</p>
            <?php endif; ?>
        </div>
        <hr/>
        <div class="product_description">
            <h3>Opis produktu:</h3></br>
            <?php echo nl2br(htmlspecialchars($product['description'])); ?>
        </div>
        <hr/>
        <div class="product_specification">
            <h3>Specyfikacja produktu:</h3></br>
            <?php echo nl2br(htmlspecialchars($product['specification'])); ?>
        </div>
        <hr/>
    </div>
<?php else: ?>
    <p>Produkt nie został znaleziony.</p>
<?php endif; ?>
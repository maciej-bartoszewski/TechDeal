<?php
require 'db_connect.php';
global $mysqli;

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$cartItems = [];

// Użytkownik zalogowany
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    // Pobranie danych z bazy, dotyczących produktów w koszyku i zapisanie ich w tabeli $cartItems
    $query = "SELECT p.product_id, p.product_name, p.image_path, p.price, c.quantity
              FROM cart_items c
              JOIN products p ON c.product_id = p.product_id
              WHERE c.cart_id = (SELECT cart_id FROM carts WHERE user_id = ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
    }
    $stmt->close();
} // Użytkownik niezalogowany
else {
    if (isset($_SESSION['cart'])) {
        // Pobranie danych z sesji, dotyczących produktów w koszyku i zapisanie ich w tabeli $cartItems
        foreach ($_SESSION['cart'] as $productId => $cartItem) {
            $quantity = $cartItem['quantity'];
            $product_query = "SELECT product_id, product_name, image_path, price FROM products WHERE product_id = ?";
            $stmt = $mysqli->prepare($product_query);
            $stmt->bind_param('i', $productId);
            $stmt->execute();
            $product_result = $stmt->get_result();
            $product = $product_result->fetch_assoc();

            if ($product) {
                $cartItems[] = [
                    'product_id' => $product['product_id'],
                    'product_name' => $product['product_name'],
                    'image_path' => $product['image_path'],
                    'price' => $product['price'],
                    'quantity' => $quantity
                ];
            }
        }
    }
}

// Zliczenie wartości zamówienia
$totalPrice = 0;
foreach ($cartItems as $item) {
    $itemTotalPrice = (float)$item['price'] * (int)$item['quantity'];
    $totalPrice += $itemTotalPrice;
}
?>

<h2>Twój koszyk</h2>
<div class="main_cart_container">
    <?php if (empty($cartItems)): ?>
        <div class="empty_cart_message">
            <p>Koszyk jest pusty</p>
        </div>
    <?php else: ?>
        <div class="left_shopping_cart_container">
            <?php foreach ($cartItems as $item): ?>
                <?php
                $itemTotalPrice = (float)$item['price'] * (int)$item['quantity'];
                ?>
                <div class="product" data-price="<?php echo htmlspecialchars($item['price'], ENT_QUOTES, 'UTF-8'); ?>"
                     data-product-id="<?php echo htmlspecialchars($item['product_id'], ENT_QUOTES, 'UTF-8'); ?>"
                     data-stock-quantity="<?php echo htmlspecialchars($item['stock_quantity'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="cart_product_info">
                        <img class="cart_product_img"
                             src="<?php echo htmlspecialchars($item['image_path'], ENT_QUOTES, 'UTF-8'); ?>"
                             alt="Zdjęcie produktu"/>
                        <h3 class="product_name"><?php echo htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    </div>
                    <div class="right_product_info_container">
                        <form class="quantity_container" method="POST" action="pages/shopping_cart/update_cart.php">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="product_id"
                                   value="<?php echo htmlspecialchars($item['product_id'], ENT_QUOTES, 'UTF-8'); ?>">

                            <button type="submit" name="quantity_decrease" value="1" class="quantity_btn">−</button>
                            <input type="number" name="quantity"
                                   value="<?php echo htmlspecialchars($item['quantity'], ENT_QUOTES, 'UTF-8'); ?>"
                                   class="quantity_input" readonly/>
                            <button type="submit" name="quantity_increase" value="1" class="quantity_btn">+</button>
                        </form>

                        <div class="price_info">
                            <h4 class="item_price"
                                data-item-total="<?php echo htmlspecialchars(number_format($itemTotalPrice, 2, '.', ''), ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars(number_format($itemTotalPrice, 2, ',', ' ') . ' zł', ENT_QUOTES, 'UTF-8'); ?>
                            </h4>
                            <p class="single_item_price">za
                                sztukę <?php echo htmlspecialchars(number_format($item['price'], 2, ',', ' ') . ' zł', ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        <form method="POST" action="pages/shopping_cart/delete_cart_item.php">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['product_id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit">
                                <img class="delete_icon" src="assets/icons/delete_gray.png" alt="Ikona usuwania"/>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="right_shopping_cart_container">
            <div class="price_summary_container">
                <div class="upper_price_summary">
                    <div class="items_price_summary">
                        <h3>Wartość produktów:</h3>
                        <p id="total_products_price"><?php echo htmlspecialchars(number_format($totalPrice, 2, ',', ' ') . ' zł', ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="items_price_summary">
                        <h3>Dostawa:</h3>
                        <p id="delivery_price">0,00 zł</p>
                    </div>
                </div>
                <hr/>
                <div class="price_summary">
                    <h3>Razem z dostawą:</h3>
                    <p id="total_price"><?php echo htmlspecialchars(number_format($totalPrice, 2, ',', ' ') . ' zł', ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
            <a class="gray_btn" href="index.php?page=shopping_cart&subpage=delivery">Dostawa i płatność</a>
        </div>
    <?php endif; ?>
</div>
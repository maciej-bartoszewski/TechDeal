<?php
require 'db_connect.php';
global $mysqli;

$subpage = $_GET['subpage'] ?? '';
$category_map = [
    'smartphones' => 'Smartfony',
    'laptops' => 'Laptopy',
    'tablets' => 'Tablety',
    'smartwatches' => 'Smartwatche'
];
$category_name = $category_map[$subpage];

// Wyczyszczenie filtorw
if (isset($_GET['clear_filters'])) {
    header('Location: ' . $_SERVER['PHP_SELF'] . '?page=shop&subpage=' . htmlspecialchars($subpage, ENT_QUOTES, 'UTF-8'));
    exit;
}

$selected_brands = $_GET['brand'] ?? [];
$min_price = $_GET['min-price'] ?? '';
$max_price = $_GET['max-price'] ?? '';
$sort = $_GET['sort'] ?? 'name_asc';

// Pobranie danych dotyczących producentów
$brand_query = "
    SELECT DISTINCT p.producer_id, pr.producer_name
    FROM products p
    JOIN producers pr ON p.producer_id = pr.producer_id
    WHERE p.category_id = (SELECT category_id FROM categories WHERE category_name = ?)
";
$stmt_brand = $mysqli->prepare($brand_query);
$stmt_brand->bind_param("s", $category_name);
$stmt_brand->execute();
$brands_result = $stmt_brand->get_result();
$stmt_brand->close();

$brand_query = !empty($selected_brands) ? 'AND p.producer_id IN (' . implode(',', array_map('intval', $selected_brands)) . ')' : '';
$price_filter = [];
if ($min_price) {
    $price_filter[] = "p.price >= $min_price";
}
if ($max_price) {
    $price_filter[] = "p.price <= $max_price";
}
$price_query = !empty($price_filter) ? 'AND ' . implode(' AND ', $price_filter) : '';
$sort_options = [
    'price_asc' => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'name_asc' => 'p.product_name ASC',
    'name_desc' => 'p.product_name DESC',
    'newest' => 'p.product_name ASC'
];
$sort_query = 'ORDER BY ' . ($sort_options[$sort] ?? $sort_options['name_asc']);

// Pobranie danych dotyczących produktów z ustawionymi filtrami
$product_query = "
    SELECT * FROM products p
    WHERE p.category_id = (SELECT category_id FROM categories WHERE category_name = ?)
    $brand_query
    $price_query
    $sort_query
";
$stmt_product = $mysqli->prepare($product_query);
$stmt_product->bind_param("s", $category_name);
$stmt_product->execute();
$products_result = $stmt_product->get_result();
$stmt_product->close();
?>

<div class="products_section">
    <div class="shop_menu">
        <button class="toggle_sort_btn">Sortowanie</button>
        <button class="toggle_filters_btn">Filtry</button>
    </div>

    <div class="product_section_container">
        <form method="GET" action="index.php">
            <input type="hidden" name="page" value="shop">
            <input type="hidden" name="subpage" value="<?php echo htmlspecialchars($subpage, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="shop_options">
                <div class="sort_section">
                    <h3>Sortuj według</h3>
                    <select class="sort_select" name="sort" id="sort">
                        <option value="name_asc" <?php if ($sort == 'name_asc') echo 'selected'; ?>>Nazwa: Rosnąco
                        </option>
                        <option value="name_desc" <?php if ($sort == 'name_desc') echo 'selected'; ?>>Nazwa: Malejąco
                        </option>
                        <option value="price_asc" <?php if ($sort == 'price_asc') echo 'selected'; ?>>Cena: Rosnąco
                        </option>
                        <option value="price_desc" <?php if ($sort == 'price_desc') echo 'selected'; ?>>Cena: Malejąco
                        </option>
                        <option value="newest" <?php if ($sort == 'newest') echo 'selected'; ?>>Najnowsze</option>
                    </select>
                    <button type="submit" class="gray_btn">Sortuj</button>
                </div>

                <div class="filter_section">
                    <div class="filter_group">
                        <h3>Marka</h3>
                        <?php if ($brands_result->num_rows != 0): ?>
                            <?php while ($brand = $brands_result->fetch_assoc()): ?>
                                <label>
                                    <input type="checkbox" name="brand[]"
                                           value="<?php echo htmlspecialchars($brand['producer_id'], ENT_QUOTES, 'UTF-8'); ?>" <?php if (in_array($brand['producer_id'], $selected_brands)) echo 'checked'; ?> />
                                    <span class="checkbox"></span> <?php echo htmlspecialchars($brand['producer_name'], ENT_QUOTES, 'UTF-8'); ?>
                                </label>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                    <hr/>
                    <div class="filter_group">
                        <h3>Cena</h3>
                        <label for="min-price">Minimalna cena:</label>
                        <input type="number" id="min-price" name="min-price" placeholder="0" min="0"
                               value="<?php echo htmlspecialchars($min_price, ENT_QUOTES, 'UTF-8'); ?>"/>
                        <label for="max-price">Maksymalna cena:</label>
                        <input type="number" id="max-price" name="max-price" placeholder="10 000" min="0"
                               value="<?php echo htmlspecialchars($max_price, ENT_QUOTES, 'UTF-8'); ?>"/>
                    </div>
                    <hr/>
                    <button type="submit" class="gray_btn">Filtruj</button>
                    <button type="submit" name="clear_filters" value="1" class="gray_btn">Wyczyść filtry</button>
                </div>
            </div>
        </form>
        <div class="products_container">
            <div class="products">
                <?php if ($products_result->num_rows != 0): ?>
                    <?php while ($product = $products_result->fetch_assoc()): ?>
                        <div class="product_card"
                             id="product_<?php echo htmlspecialchars($product['product_id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <img src="<?php echo htmlspecialchars($product['image_path'], ENT_QUOTES, 'UTF-8'); ?>"
                                 alt="<?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?>"
                                 class="product_image"/>
                            <h3 class="product_title"><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p class="product_price"><?php echo htmlspecialchars(number_format($product['price'], 2, ',', ' ') . ' zł', ENT_QUOTES, 'UTF-8'); ?></p>
                            <div class="product_links">
                                <a href="index.php?page=shop&subpage=product&id=<?php echo htmlspecialchars($product['product_id'], ENT_QUOTES, 'UTF-8'); ?>"
                                   class="product_link">Dowiedz się więcej</a>
                                <?php if ($product['stock_quantity'] != 0): ?>
                                    <a href="pages/shopping_cart/add_to_cart.php?id=<?php echo htmlspecialchars($product['product_id'], ENT_QUOTES, 'UTF-8'); ?>#product_<?php echo htmlspecialchars($product['product_id'], ENT_QUOTES, 'UTF-8'); ?>"
                                       class="product_link gray add-to-cart">Dodaj do koszyka</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Brak dostępnych produktów.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
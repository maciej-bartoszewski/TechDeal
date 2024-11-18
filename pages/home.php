<?php
require 'db_connect.php';
global $mysqli;

// Pobranie informacji o ostatnio dodanych produkach do bazy
$latest_products_query = "SELECT * FROM products ORDER BY added_at DESC LIMIT 4";
$latest_products_result = $mysqli->query($latest_products_query);

// Pobranie informacji o najlepiej sprzedających się produktach z bazy
$best_sellers_query = "
    SELECT p.*, SUM(oi.quantity) AS total_sales 
    FROM products p 
    JOIN order_items oi ON p.product_id = oi.product_id 
    GROUP BY p.product_id 
    ORDER BY total_sales DESC 
    LIMIT 4";
$best_sellers_result = $mysqli->query($best_sellers_query);

// Pobranie informacji o producentach z bazy
$producers_query = "SELECT producer_name, image_path FROM producers";
$producers_result = $mysqli->query($producers_query);
?>

    <div class="home_container">
        <div class="slider">
            <div class="slides">
                <div class="slide">
                    <div class="slide_text item_left">
                        <h3>Smartfony, które robią różnicę</h3>
                        <p>Najnowsze modele w super cenach!</p>
                    </div>
                    <img src="assets/products/samsung_s24.png" alt="Smartfony" class="item_right"/>
                </div>
                <div class="slide">
                    <img src="assets/products/matebook.png" alt="Laptopy" class="item_left"/>
                    <div class="slide_text item_right">
                        <h3>Najlepsze oferty na laptopy</h3>
                        <p>HUAWEI MateBook X Pro już w sprzedaży!</p>
                    </div>
                </div>
                <div class="slide">
                    <div class="slide_text item_left">
                        <h3>Odkryj moc nowoczesnej technologii</h3>
                        <p>Tablety, które sprostają każdemu wyzwaniu!</p>
                    </div>
                    <img src="assets/products/huawei_matepad.png" alt="Tablety" class="item_right"/>
                </div>
                <div class="slide">
                    <img src="assets/products/watch_ultimate.png" alt="Smartwatche" class="item_left"/>
                    <div class="slide_text item_right">
                        <h3>Inteligencja na Twoim nadgarstku</h3>
                        <p>Smartwatche, które motywują do działania!</p>
                    </div>
                </div>
            </div>
            <button class="arrow left">&#10094;</button>
            <button class="arrow right">&#10095;</button>
        </div>

        <div class="bestselers">
            <h3>Najczęściej kupowane produkty</h3>
            <div class="products">
                <?php if ($best_sellers_result->num_rows > 0): ?>
                    <?php while ($product = $best_sellers_result->fetch_assoc()): ?>
                        <div class="product_card" id="product_<?php echo htmlspecialchars($product['product_id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <img src="<?php echo htmlspecialchars($product['image_path'], ENT_QUOTES, 'UTF-8'); ?>"
                                 alt="<?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?>" class="product_image"/>
                            <h3 class="product_title"><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p class="product_price"><?php echo number_format($product['price'], 2, ',', ' ') . ' zł'; ?></p>
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
                    <p>Brak najczęściej kupowanych produktów.</p>
                <?php endif; ?>
            </div>
        </div>


        <div class="latest">
            <h3>Najnowsze produkty</h3>
            <div class="products">
                <?php if ($latest_products_result->num_rows > 0): ?>
                    <?php while ($product = $latest_products_result->fetch_assoc()): ?>
                        <div class="product_card" id="product_<?php echo htmlspecialchars($product['product_id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <img src="<?php echo htmlspecialchars($product['image_path'], ENT_QUOTES, 'UTF-8'); ?>"
                                 alt="<?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?>" class="product_image"/>
                            <h3 class="product_title"><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p class="product_price"><?php echo number_format($product['price'], 2, ',', ' ') . ' zł'; ?></p>
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
                    <p>Brak nowych produktów.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="why_us">
            <h3 class="why_us_header">Dlaczego warto nas wybrać?</h3>
            <div class="why_us_benefits">
                <div class="benefit">
                    <img src="assets/icons/truck.png" alt="Ciężarówka"/>
                    <h4>Darmowa Dostawa</h4>
                    <p>Darmowa dostawa na terenie całego kraju.</p>
                </div>
                <div class="benefit">
                    <img src="assets/icons/lock.png" alt="Kłódka"/>
                    <h4>Bezpieczne Płatności</h4>
                    <p>Zapewniamy szyfrowane i bezpieczne metody płatności.</p>
                </div>
                <div class="benefit">
                    <img src="assets/icons/headphones.png" alt="Słuchawki"/>
                    <h4>24/7 Obsługa Klienta</h4>
                    <p>Jesteśmy tutaj, aby pomóc Ci przez całą dobę, siedem dni w tygodniu.</p>
                </div>
                <div class="benefit">
                    <img src="assets/icons/thumbs_up.png" alt="Łapka w górę"/>
                    <h4>Gwarancja Satysfakcji</h4>
                    <p>Pełne zadowolenie z zakupów lub zwrot pieniędzy.</p>
                </div>
            </div>
        </div>

        <div class="brands">
            <h3>Najlepsze marki dla Ciebie</h3>
            <div class="brands_images">
                <?php if ($producers_result->num_rows > 0) {
                    while ($row = $producers_result->fetch_assoc()) {
                        echo '<img src="' . htmlspecialchars($row['image_path'], ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($row['producer_name'], ENT_QUOTES, 'UTF-8') . ' logo"/>';
                    }
                } else {
                    echo "<p>Brak dostępnych marek.</p>";
                } ?>
            </div>
        </div>

        <div class="testimonial_container">
            <h3>Opinie naszych klientów</h3>
            <div class="testimonials">
                <div class="testimonial">
                    <p class="quote">
                        "Ten sklep oferuje imponujący wybór produktów w najlepszych cenach. Proces zakupu był
                        bezproblemowy, a zespół
                        wsparcia niezwykle pomocny. Jestem zachwycony moim nowym laptopem!"
                    </p>
                    <p class="author">- Marek Kowalski, Programista</p>
                </div>
                <div class="testimonial">
                    <p class="quote">
                        "Kupiłam tutaj smartwatcha, który przewyższył moje oczekiwania. Jakość jest doskonała, a dostawa
                        była bardzo
                        szybka. Na pewno tu wrócę po więcej!"
                    </p>
                    <p class="author">- Sara Nowak, Miłośniczka Fitnessu</p>
                </div>
                <div class="testimonial">
                    <p class="quote">
                        "Świetna obsługa klienta i wspaniała różnorodność produktów. Znalazłem dokładnie to, czego
                        potrzebowałem, a
                        nawigacja po stronie była bardzo łatwa. Gorąco polecam!"
                    </p>
                    <p class="author">- Dawid Wiśniewski, Konsultant Biznesowy</p>
                </div>
            </div>
        </div>
    </div>

    <script src="scripts/scroll_position.js"></script>

<?php
$latest_products_result->close();
$best_sellers_result->close();
$producers_result->close();
?>
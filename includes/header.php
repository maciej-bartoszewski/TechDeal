<?php if (isset($_SESSION['user_id'])): ?>
    <?php
    // Użytkownik zalogowany
    require_once('db_connect.php');
    global $mysqli;
    // Pobranie informacji o ilości produktów w koszyku użytkownika z bazy
    $user_id = $_SESSION['user_id'];
    $cart_query = $mysqli->prepare("SELECT SUM(quantity) AS total_quantity FROM cart_items ci JOIN carts c ON ci.cart_id = c.cart_id WHERE c.user_id = ?");
    $cart_query->bind_param("i", $user_id);
    $cart_query->execute();
    $cart_result = $cart_query->get_result();
    $cart_data = $cart_result->fetch_assoc();
    $total_quantity = $cart_data['total_quantity'] ?: 0;
    ?>
<?php else: ?>
    <?php
    // Użytkownik niezalogowany
    // Pobranie informacji o ilości produktów w koszyku użytkownika z sesji
    $total_quantity = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
    ?>
<?php endif; ?>

<nav class="nav_container">
    <div class="header_contact">
        <div class="contant_item">
            <img src="assets/icons/phone.png" alt="Ikona telefonu"/>
            <p><a href="tel:+48123456789">+48 123 456 789</a></p>
        </div>
        <div class="contant_item">
            <img src="assets/icons/mail.png" alt="Ikona listu"/>
            <p><a href="mailto:kontakt@techdeal.pl">kontakt@techdeal.pl</a></p>
        </div>
    </div>

    <div class="main_nav_container">
        <a href="index.php?page=home" class="logo_container">
            <h1 class="logo_name">Tech<span>Deal</span></h1>
        </a>

        <div class="middle_header_container">
            <a href="index.php?page=shop&subpage=smartphones">Smartfony</a>
            <a href="index.php?page=shop&subpage=laptops">Laptopy</a>
            <a href="index.php?page=shop&subpage=tablets">Tablety</a>
            <a href="index.php?page=shop&subpage=smartwatches">Smartwatche</a>
        </div>

        <div class="right_header_container">
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                <a id="admin_panel" href="index.php?page=admin" class="nav_info_container">
                    <img src="assets/icons/admin.png" alt="Ikonka panelu administratora"/>
                </a>
            <?php endif; ?>

            <a href="index.php?page=shopping_cart&subpage=general" class="nav_info_container">
                <img src="assets/icons/bag.png" alt="Ikonka koszyka"/>
                <span class="cart_count"><?php echo $total_quantity; ?></span>
            </a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="index.php?page=account" class="nav_info_container">
                    <img src="assets/icons/account.png" alt="Ikonka konta użytkownika"/>
                </a>
            <?php else: ?>
                <a href="index.php?page=login" class="nav_info_container">
                    <img src="assets/icons/account.png" alt="Ikonka konta użytkownika"/>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="mobile_second_nav">
        <a href="index.php?page=shop&subpage=smartphones">Smartfony</a>
        <a href="index.php?page=shop&subpage=laptops">Laptopy</a>
        <a href="index.php?page=shop&subpage=tablets">Tablety</a>
        <a href="index.php?page=shop&subpage=smartwatches">Smartwatche</a>
    </div>
</nav>
<div class="cart_container">
    <?php
    if (isset($_GET['subpage'])) {
        $subpage = $_GET['subpage'];

        if ($subpage == 'general') {
            include('pages/shopping_cart/shopping_cart_summary.php');
        }

        else if ($subpage == 'delivery') {
            // Jeśli użytkownik jest zalogowany to przenieś do dostawy, a jeśli nie to do logowania
            if (isset($_SESSION['user_id'])) {
                include('pages/shopping_cart/shopping_cart_delivery.php');
            } else {
                header("Location: index.php?page=login");
                exit();
            }
        }

        else if ($subpage == 'confirmation') {
            // Jeśli użytkownik jest zalogowany to przenieś do podsumowania, a jeśli nie to do logowania
            if (isset($_SESSION['user_id'])) {
                include('pages/shopping_cart/shopping_cart_confirmation.html');
            } else {
                header("Location: index.php?page=login");
                exit();
            }
        } else {
            include('pages/error_page.html');
        }
    }
    ?>
</div>
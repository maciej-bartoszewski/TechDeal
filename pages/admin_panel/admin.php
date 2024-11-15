<?php
ob_start();
?>
<div class="admin_container">
    <div class="admin_links_container" id="admin_sidebar">
        <a href="index.php?page=admin&subpage=users_list">
            <img src="assets/icons/users.png" alt="Ikonka użytkowników"/> Użytkownicy
        </a>
        <a href="index.php?page=admin&subpage=orders_list">
            <img src="assets/icons/document.png" alt="Ikonka zamówień"/> Zamówienia
        </a>
        <a href="index.php?page=admin&subpage=products_list">
            <img src="assets/icons/products.png" alt="Ikonka produktów"/> Produkty
        </a>
        <a href="index.php?page=admin&subpage=producers_list">
            <img src="assets/icons/producers.png" alt="Ikonka producentów"/> Producenci
        </a>
        <a href="index.php?page=admin&subpage=categories_list">
            <img src="assets/icons/categories.png" alt="Ikonka kategorii"/> Kategorie
        </a>
        <a href="index.php?page=admin&subpage=payments_list">
            <img src="assets/icons/payments.png" alt="Ikonka płatności"/> Płatności
        </a>
    </div>

    <div class="admin_page">
        <?php
        if (isset($_GET['subpage'])) {
            $subpage = $_GET['subpage'];
            if ($subpage == 'users_list') {
                include('pages/admin_panel/users/users_list.php');
            } else if ($subpage == 'user_add') {
                include('pages/admin_panel/users/user_add.php');
            } else if ($subpage == 'user_edit') {
                include('pages/admin_panel/users/user_edit.php');
            }

            else if ($subpage == 'products_list') {
                include('pages/admin_panel/products/products_list.php');
            } else if ($subpage == 'product_add' || $subpage == 'product_edit') {
                include('pages/admin_panel/products/product_add_edit.php');
            }

            else if ($subpage == 'producers_list') {
                include('pages/admin_panel/producers/producers_list.php');
            } else if ($subpage == 'producer_add' || $subpage == 'producer_edit') {
                include('pages/admin_panel/producers/producer_add_edit.php');
            }

            else if ($subpage == 'payments_list') {
                include('pages/admin_panel/payments/payments_list.php');
            } else if ($subpage == 'payment_add' || $subpage == 'payment_edit') {
                include('pages/admin_panel/payments/payment_add_edit.php');
            }

            else if ($subpage == 'categories_list') {
                include('pages/admin_panel/categories/categories_list.php');
            } else if ($subpage == 'category_add' || $subpage == 'category_edit') {
                include('pages/admin_panel/categories/category_add_edit.php');
            }

            else if ($subpage == 'orders_list') {
                include('pages/admin_panel/orders/orders_list.php');
            } else if ($subpage == 'order_details') {
                include('pages/admin_panel/orders/order_details.php');
            }

            else {
                include('pages/error_page.html');
            }
        } else {
            include('pages/admin_panel/users/users_list.php');
        }
        ?>
    </div>
</div>
<?php
ob_end_flush();
?>
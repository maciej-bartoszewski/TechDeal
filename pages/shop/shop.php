<div class="shop_container">
    <?php
    if (isset($_GET['subpage'])) {
        $subpage = $_GET['subpage'];
        if (in_array($subpage, ['smartphones', 'laptops', 'tablets', 'smartwatches'])) {
            include('pages/shop/products.php');
        } else if ($subpage == 'product') {
            include('pages/shop/product.php');
        } else {
            include('pages/error_page.html');
        }
    }
    ?>
</div>
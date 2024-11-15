<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>TechDeal</title>
    <link rel="shortcut icon" type="image/png" href="assets/icons/logo.png">
    <link rel="stylesheet" href="includes/style/style.css"/>
    <link rel="stylesheet" href="includes/style/login_register.css"/>
    <link rel="stylesheet" href="includes/style/header.css"/>
    <link rel="stylesheet" href="includes/style/footer.css"/>
    <link rel="stylesheet" href="includes/style/account.css"/>
    <link rel="stylesheet" href="includes/style/address.css"/>
    <link rel="stylesheet" href="includes/style/shopping_cart.css"/>
    <link rel="stylesheet" href="includes/style/home.css"/>
    <link rel="stylesheet" href="includes/style/slider.css"/>
    <link rel="stylesheet" href="includes/style/product.css"/>
    <link rel="stylesheet" href="includes/style/shop.css"/>
    <link rel="stylesheet" href="includes/style/product_grid.css"/>
    <link rel="stylesheet" href="includes/style/admin.css"/>
</head>

<body>
<?php
ini_set('session.gc_maxlifetime', 7 * 24 * 60 * 60);
session_start();

$session_lifetime = 3 * 60 * 60;
$cart_lifetime = 7 * 24 * 60 * 60;

// Przechowywanie informacji o zalogowaniu użytkownika - 3h
if (isset($_SESSION['last_activity'])) {
    // Usunięcie informacji
    if (time() - $_SESSION['last_activity'] > $session_lifetime) {
        unset($_SESSION['user_id']);
        unset($_SESSION['is_admin']);
    } else {
        // Aktualizowanie czasu przechowywania informacji o zalogowaniu użytkownika
        $_SESSION['last_activity'] = time();
    }
} else {
    // Zapisanie czasu
    $_SESSION['last_activity'] = time();
}

// Przechowywanie informacji o koszyku - 7 dni
if (isset($_SESSION['cart_created'])) {
    if (time() - $_SESSION['cart_created'] > $cart_lifetime) {
        // Usunięcie informacji
        unset($_SESSION['cart']);
        unset($_SESSION['cart_created']);
    } else {
        // Aktualizowanie czasu przechowywania informacji o koszyku
        $_SESSION['cart_created'] = time();
    }
} else {
    // Zapisanie czasu
    $_SESSION['cart_created'] = time();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

include('includes/header.php');
?>

<div id="alert-container"></div>
<script src="scripts/alerts.js"></script>
<?php if (isset($_SESSION['info_message']) || isset($_SESSION['error_message'])): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            <?php if (isset($_SESSION['info_message'])): ?>
            showAlert("<?php echo $_SESSION['info_message']; ?>", "info");
            <?php unset($_SESSION['info_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
            showAlert("<?php echo $_SESSION['error_message']; ?>", "error");
            <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
        });
    </script>
<?php endif; ?>


<div class="content_container">
    <?php
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
        switch ($page) {
            case 'home':
                include('pages/home.php');
                echo '<script src="scripts/slider.js"></script>';
                break;

            case 'login':
                include('pages/login.php');
                echo '<script src="scripts/validation/validateLogin.js"></script>';
                break;

            case 'register':
                include('pages/register.php');
                echo '<script src="scripts/validation/validateRegistration.js"></script>';
                break;

            case 'account':
                if (isset($_SESSION['user_id'])) {
                    include('pages/account/account.php');
                } else {
                    header("Location: index.php?page=login");
                    exit;
                }
                break;
            case 'shop':
                include('pages/shop/shop.php');
                echo '<script src="scripts/open_sort_filters_mobile.js"></script>';
                break;

            case 'shopping_cart':
                include('pages/shopping_cart/shopping_cart.php');
                echo '<script src="scripts/change_address_payment.js"></script>';
                break;

            case 'admin':
                if (isset($_SESSION['is_admin'])) {
                    include('pages/admin_panel/admin.php');
                    echo '<script src="scripts/image_update.js"></script>';
                } else {
                    header("Location: index.php");
                    exit;
                }
                break;

            default:
                include('pages/error_page.html');
                break;
        }
    } else {
        include('pages/home.php');
        echo '<script src="scripts/slider.js"></script>';
    }
    ?>

</div>
<?php include('includes/footer.html'); ?>
</body>

</html>
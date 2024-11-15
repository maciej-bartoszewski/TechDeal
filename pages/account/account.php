<?php
ob_start();
?>
<div class="account_container">
    <div class="account_main_container">
        <div class="account_left_container">
            <div class="account_links_container">
                <a href="index.php?page=account&subpage=orders">
                    <img src="assets/icons/document.png" alt="Ikonka zamówień" />Zamówienia
                </a>
                <a href="index.php?page=account&subpage=general">
                    <img src="assets/icons/account.png" alt="Ikonka konta" />Dane konta
                </a>
                <a href="index.php?page=account&subpage=address">
                    <img src="assets/icons/location.png" alt="Ikonka adresu" />Adresy
                </a>
                <a href="index.php?page=account&subpage=password">
                    <img src="assets/icons/password.png" alt="Ikonka hasła" />Zmień hasło
                </a>
                <a href="pages/account/logout.php" class="logout">
                    <img src="assets/icons/logout.png" alt="Ikonka wylogowania" />Wyloguj
                </a>
            </div>
        </div>

        <div class="account_right_container">
            <?php
            if (isset($_GET['subpage'])) {
                $subpage = $_GET['subpage'];

                switch ($subpage) {
                    case 'orders':
                        include('pages/account/orders.php');
                        break;
                    case 'order_details':
                        include('pages/account/order_details.php');
                        break;
                    case 'general':
                        include('pages/account/general.php');
                        echo '<script src="scripts/validation/validateAccountUpdate.js"></script>';
                        break;
                    case 'address':
                        include('pages/account/address/address.php');
                        break;
                    case 'address_add':
                    case 'address_edit':
                        include('pages/account/address/address_add_edit.php');
                        echo '<script src="scripts/validation/validateAddress.js"></script>';
                        break;
                    case 'password':
                        include('pages/account/password.php');
                        echo '<script src="scripts/validation/validatePasswordChange.js"></script>';
                        break;
                    default:
                        include('pages/error_page.html');
                        break;
                }
            } else {
                include('pages/account/orders.php');
            }
            ?>
        </div>
    </div>
</div>
<?php
ob_end_flush();
?>
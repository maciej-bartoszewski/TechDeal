<?php
require 'db_connect.php';
global $mysqli;

$errors = [];
$email = $password = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['e-mail']);
    $password = $_POST['password'];

    $emailPattern = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';

    // Walidacja
    if (empty($email)) {
        $errors['email'] = 'E-mail jest wymagany.';
    } elseif (!preg_match($emailPattern, $email)) {
        $errors['email'] = 'Niepoprawny adres e-mail.';
    }

    if (empty($password)) {
        $errors['password'] = 'Hasło jest wymagane.';
    }

    if (empty($errors)) {
        // Pobranie informacji o użytkowniku z bazy
        $stmt = $mysqli->prepare("SELECT user_id, password, is_admin FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // Konto użytkownika istnieje
        if ($stmt->num_rows != 0) {
            $user_id = $hashed_password = $is_admin = null;
            $stmt->bind_result($user_id, $hashed_password, $is_admin);
            $stmt->fetch();

            // Weryfikacja hasła
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $user_id;

                if ($is_admin == 1) {
                    $_SESSION['is_admin'] = true;
                }

                if (isset($_SESSION['cart'])) {
                    // Pobranie cart_id (koszyku) użytkownika
                    $cart_stmt = $mysqli->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
                    $cart_stmt->bind_param("i", $user_id);
                    $cart_stmt->execute();
                    $cart_id = null;
                    $cart_stmt->bind_result($cart_id);
                    $cart_stmt->fetch();
                    $cart_stmt->close();

                    // Iteracja po każdym produkcie który znajduje się w koszyku w sesji
                    foreach ($_SESSION['cart'] as $productId => $cartItem) {
                        $quantity = $cartItem['quantity'];
                        $check_item_stmt = $mysqli->prepare("SELECT quantity FROM cart_items WHERE product_id = ? AND cart_id = ?");
                        $check_item_stmt->bind_param("ii", $productId, $cart_id);
                        $check_item_stmt->execute();
                        $existing_quantity = null;
                        $check_item_stmt->bind_result($existing_quantity);

                        if ($check_item_stmt->fetch()) {
                            // Produkt znajduje się już w koszyku, więc następuje aktualizacja jego ilości w bazie
                            $new_quantity = $existing_quantity + $quantity;
                            $check_item_stmt->close();
                            $update_item_stmt = $mysqli->prepare("UPDATE cart_items SET quantity = ? WHERE product_id = ? AND cart_id = ?");
                            $update_item_stmt->bind_param("iii", $new_quantity, $productId, $cart_id);
                            $update_item_stmt->execute();
                            $update_item_stmt->close();
                        } else {
                            // Brak produktu w koszyku, więc produkt zostaje dodany do bazy
                            $check_item_stmt->close();
                            $insert_item_stmt = $mysqli->prepare("INSERT INTO cart_items (product_id, cart_id, quantity) VALUES (?, ?, ?)");
                            $insert_item_stmt->bind_param('iii', $productId, $cart_id, $quantity);
                            $insert_item_stmt->execute();
                            $insert_item_stmt->close();
                        }
                    }
                    unset($_SESSION['cart']);
                }
                $_SESSION['info_message'] = 'Zalogowano pomyślnie.';
                header('Location: http://localhost/techdeal/index.php?page=account');
            } else {
                $errors['password'] = 'Niepoprawne hasło.';
            }
        } else {
            $errors['email'] = 'Konto z tym adresem e-mail nie istnieje.';
        }

        $stmt->close();
    }
}
?>

<div class="login_register_container">
    <h2>Zaloguj się</h2>

    <form action="index.php?page=login" onsubmit="return validateLogin()" method="POST">
        <div class="form_group">
            <label for="e-mail">E-mail</label>
            <input type="email" id="e-mail" name="e-mail" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>"/>
            <span class="error"><?= $errors['email'] ?? '' ?></span>
        </div>

        <div class="form_group">
            <label for="password">Hasło</label>
            <input type="password" id="password" name="password"/>
            <span class="error"><?= $errors['password'] ?? '' ?></span>
        </div>

        <button type="submit" class="red_button">Logowanie</button>
    </form>

    <p>Nie masz konta? <a href="index.php?page=register">Zarejestruj się</a></p>
</div>
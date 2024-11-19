<?php
require 'db_connect.php';
global $mysqli;

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$query = "SELECT * FROM payments";
$result = $mysqli->query($query);
?>

<div class="main_page_header">
    <h3>Metody Płatności</h3>
    <a href="index.php?page=admin&subpage=payment_add" class="add-btn">
        <img src="assets/icons/add.png" alt="Dodaj"/> Dodaj
    </a>
</div>

<div class="main_page_container">
    <?php if ($result->num_rows == 0): ?>
        <p>Brak metod płatności.</p>
    <?php else: ?>
        <?php while ($payment = $result->fetch_assoc()): ?>
            <div class="data_container">
                <div class="data_info">
                    <p>Metoda płatności: <strong><?= htmlspecialchars($payment['payment_method'], ENT_QUOTES, 'UTF-8') ?> </strong></p>
                    <img src="<?= htmlspecialchars($payment['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="Obraz metody płatności"
                         class="data_img"/>
                </div>
                <div class="data_actions">
                    <a href="index.php?page=admin&subpage=payment_edit&payment_id=<?= htmlspecialchars($payment['payment_id'], ENT_QUOTES, 'UTF-8') ?>"
                       class="edit-btn">
                        <img src="assets/icons/edit.png" alt="Edytuj"/>Edytuj</a>
                    <form method="POST" action="pages/admin_panel/payments/payment_delete.php" class="delete-form">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="payment_id" value="<?= htmlspecialchars($payment['payment_id'], ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="delete-btn">
                            <img src="assets/icons/delete.png" alt="Usuń"/> Usuń
                        </button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>
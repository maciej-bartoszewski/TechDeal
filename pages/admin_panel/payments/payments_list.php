<?php
require 'db_connect.php';
global $mysqli;

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
                    <p>Metoda płatności: <strong><?= htmlspecialchars($payment['payment_method']) ?> </strong></p>
                    <img src="<?= htmlspecialchars($payment['image_path']) ?>" alt="Obraz metody płatności"
                         class="data_img"/>
                </div>
                <div class="data_actions">
                    <a href="index.php?page=admin&subpage=payment_edit&payment_id=<?= htmlspecialchars($payment['payment_id']) ?>"
                       class="edit-btn">
                        <img src="assets/icons/edit.png" alt="Edytuj"/>Edytuj</a>
                    <a href="pages/admin_panel/payments/payment_delete.php?id=<?= htmlspecialchars($payment['payment_id']) ?>"
                       class="delete-btn">
                        <img src="assets/icons/delete.png" alt="Usuń"/> Usuń
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>
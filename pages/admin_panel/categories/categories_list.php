<?php
require 'db_connect.php';
global $mysqli;

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$query = "SELECT * FROM categories";
$result = $mysqli->query($query);
?>

<div class="main_page_header">
    <h3>Kategorie</h3>
    <a href="index.php?page=admin&subpage=category_add" class="add-btn">
        <img src="assets/icons/add.png" alt="Dodaj"/> Dodaj
    </a>
</div>

<div class="main_page_container">
    <?php if ($result->num_rows == 0): ?>
        <p>Brak kategorii.</p>
    <?php else: ?>
        <?php while ($category = $result->fetch_assoc()): ?>
            <div class="data_container">
                <div class="data_info">
                    <p>Kategoria: <strong><?= htmlspecialchars($category['category_name'], ENT_QUOTES, 'UTF-8') ?> </strong></p>
                </div>
                <div class="data_actions">
                    <a href="index.php?page=admin&subpage=category_edit&category_id=<?= htmlspecialchars($category['category_id'], ENT_QUOTES, 'UTF-8') ?>"
                       class="edit-btn">
                        <img src="assets/icons/edit.png" alt="Edytuj"/>Edytuj</a>
                    <form method="POST" action="pages/admin_panel/categories/category_delete.php" class="delete-form">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="category_id" value="<?= htmlspecialchars($category['category_id'], ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="delete-btn">
                            <img src="assets/icons/delete.png" alt="Usuń"/> Usuń
                        </button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>
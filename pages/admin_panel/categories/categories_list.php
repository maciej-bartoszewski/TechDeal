<?php
require 'db_connect.php';
global $mysqli;

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
                    <p>Kategoria: <strong><?= htmlspecialchars($category['category_name']) ?> </strong></p>
                </div>
                <div class="data_actions">
                    <a href="index.php?page=admin&subpage=category_edit&category_id=<?= htmlspecialchars($category['category_id']) ?>"
                       class="edit-btn">
                        <img src="assets/icons/edit.png" alt="Edytuj"/>Edytuj</a>
                    <a href="pages/admin_panel/categories/category_delete.php?id=<?= htmlspecialchars($category['category_id']) ?>"
                       class="delete-btn">
                        <img src="assets/icons/delete.png" alt="Usuń"/> Usuń
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>
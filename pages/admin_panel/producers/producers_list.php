<?php
require 'db_connect.php';
global $mysqli;

$query = "SELECT * FROM producers";
$result = $mysqli->query($query);
?>

<div class="main_page_header">
    <h3>Producenci</h3>
    <a href="index.php?page=admin&subpage=producer_add" class="add-btn">
        <img src="assets/icons/add.png" alt="Dodaj"/> Dodaj
    </a>
</div>

<div class="main_page_container">
    <?php if ($result->num_rows == 0): ?>
        <p>Brak producentów.</p>
    <?php else: ?>
        <?php while ($producer = $result->fetch_assoc()): ?>
            <div class="data_container">
                <div class="data_info">
                    <p>Producent: <strong><?= htmlspecialchars($producer['producer_name'], ENT_QUOTES, 'UTF-8') ?> </strong></p>
                    <img src="<?= htmlspecialchars($producer['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="Obraz producenta"
                         class="data_img"/>
                </div>
                <div class="data_actions">
                    <a href="index.php?page=admin&subpage=producer_edit&producer_id=<?= htmlspecialchars($producer['producer_id'], ENT_QUOTES, 'UTF-8') ?>"
                       class="edit-btn">
                        <img src="assets/icons/edit.png" alt="Edytuj"/>Edytuj</a>
                    <a href="pages/admin_panel/producers/producer_delete.php?id=<?= htmlspecialchars($producer['producer_id'], ENT_QUOTES, 'UTF-8') ?>"
                       class="delete-btn">
                        <img src="assets/icons/delete.png" alt="Usuń"/> Usuń
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>
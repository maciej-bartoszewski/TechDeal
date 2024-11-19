<?php
require 'db_connect.php';
global $mysqli;

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$search_user = $_GET['search_user'] ?? '';

// Pobranie informacji o użytkownikach z bazy danych
$query = "SELECT * FROM users";

// Zastosowanie filtru dla "imienia nazwiska" oraz emaila przy wysukiwaniu
if ($search_user) {
    $query .= " WHERE CONCAT(first_name, ' ', last_name) LIKE ? OR
                email LIKE ?";
    $search_term = '%' . $search_user . '%';
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ss", $search_term, $search_term);
} else {
    $stmt = $mysqli->prepare($query);
}

// Sortowanie
$query .= " ORDER BY user_id ASC";

$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<div class="main_page_header">
    <h3>Użytkownicy</h3>
    <a href="index.php?page=admin&subpage=user_add" class="add-btn">
        <img src="assets/icons/add.png" alt="Dodaj"/> Dodaj
    </a>
</div>

<form class="admin_search_container" method="GET" action="index.php">
    <input type="hidden" name="page" value="admin">
    <input type="hidden" name="subpage" value="users_list">
    <input type="text" name="search_user" placeholder="Wyszukaj użytkownika" value="<?= htmlspecialchars($search_user, ENT_QUOTES, 'UTF-8') ?>"/>
    <button class="search_btn" type="submit">Szukaj</button>
</form>

<div class="main_page_container">
    <?php if ($result->num_rows == 0): ?>
        <p>Nie znaleziono użytkowników.</p>
    <?php else: ?>
        <?php while ($user = $result->fetch_assoc()): ?>
            <div class="data_container">
                <div class="data_info">
                    <p>Użytkownik: <strong><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name'], ENT_QUOTES, 'UTF-8') ?></strong></p>
                    <p>Email: <strong><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></strong></p>
                    <p>Numer tel: <strong><?= htmlspecialchars($user['phone_number'], ENT_QUOTES, 'UTF-8') ?></strong></p>
                    <p>Admin: <strong><?= htmlspecialchars($user['is_admin'] ? 'Tak' : 'Nie', ENT_QUOTES, 'UTF-8') ?></strong></p>
                </div>
                <div class="data_actions">
                    <a href="index.php?page=admin&subpage=user_edit&user_id=<?= htmlspecialchars($user['user_id'], ENT_QUOTES, 'UTF-8') ?>" class="edit-btn">
                        <img src="assets/icons/edit.png" alt="Edytuj"/> Edytuj
                    </a>
                    <form method="POST" action="pages/admin_panel/users/user_delete.php" class="delete-form">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id'], ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="delete-btn">
                            <img src="assets/icons/delete.png" alt="Usuń"/> Usuń
                        </button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>
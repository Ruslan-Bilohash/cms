<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';

header('Content-Type: application/json');

$query = $_POST['query'] ?? '';
$category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT) ?: '';

$sql = 'SELECT id, name, price, custom_url FROM shop_products WHERE status = "active" AND name LIKE ?';
if ($category_id) {
    $sql .= ' AND category_id = ?';
}

$stmt = $conn->prepare($sql);
$search_term = "%$query%";
if ($category_id) {
    $stmt->bind_param('si', $search_term, $category_id);
} else {
    $stmt->bind_param('s', $search_term);
}
$stmt->execute();
$results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode($results);
?>
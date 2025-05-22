<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']['user_id'])) {
    header('Location: login1.html');
    exit;
}

$user_id = $_SESSION['user']['user_id'];
$type = $_GET['type'] ?? 'food';
$item_id = $_GET['item_id'] ?? null;
$place_name = $_GET['place_name'] ?? null;

if ($item_id && $place_name) {
    if ($type === 'drink') {
        $stmt = $pdo->prepare("DELETE FROM drink_favorites WHERE user_id = ? AND drink_id = ? AND place_name = ?");
    } else {
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND food_id = ? AND place_name = ?");
    }
    $stmt->execute([$user_id, $item_id, $place_name]);
}

header("Location: favorites.php?type=" . urlencode($type));
exit;
?>

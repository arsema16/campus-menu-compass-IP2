<?php
session_start();
require_once '../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user']['user_id'];
$food_id = $_POST['food_id'] ?? null;
$place_name = $_POST['place_name'] ?? null;

if (!$food_id || !$place_name) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    exit;
}

// Check if already favorited
$stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = ? AND food_id = ? AND place_name = ?");
$stmt->execute([$user_id, $food_id, $place_name]);
$exists = $stmt->fetch();

if ($exists) {
    // Remove favorite
    $del = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND food_id = ? AND place_name = ?");
    $del->execute([$user_id, $food_id, $place_name]);
    echo json_encode(['status' => 'success', 'action' => 'removed']);
} else {
    // Add favorite
    $ins = $pdo->prepare("INSERT INTO favorites (user_id, food_id, place_name) VALUES (?, ?, ?)");
    $ins->execute([$user_id, $food_id, $place_name]);
    echo json_encode(['status' => 'success', 'action' => 'added']);
}

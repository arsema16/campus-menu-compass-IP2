<?php
session_start();
header('Content-Type: application/json');
require_once '../db.php';

$user_id = $_SESSION['user']['user_id'] ?? null;
$drink_id = $_POST['drink_id'] ?? null;
$place_name = $_POST['place_name'] ?? null;

if (!$user_id || !$drink_id || !$place_name) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters.']);
    exit;
}

// Check if already favorited
$stmt = $pdo->prepare("SELECT * FROM drink_favorites WHERE user_id = ? AND drink_id = ? AND place_name = ?");
$stmt->execute([$user_id, $drink_id, $place_name]);
$exists = $stmt->fetch();

if ($exists) {
    // Remove favorite
    $del = $pdo->prepare("DELETE FROM drink_favorites WHERE user_id = ? AND drink_id = ? AND place_name = ?");
    $del->execute([$user_id, $drink_id, $place_name]);
    echo json_encode(['status' => 'success', 'action' => 'removed']);
} else {
    // Add favorite
    $add = $pdo->prepare("INSERT INTO drink_favorites (user_id, drink_id, place_name) VALUES (?, ?, ?)");
    $add->execute([$user_id, $drink_id, $place_name]);
    echo json_encode(['status' => 'success', 'action' => 'added']);
}

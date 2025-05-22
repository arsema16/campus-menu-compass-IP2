<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']['user_id'])) {
    http_response_code(403);
    echo "You must be logged in.";
    exit;
}

$user_id = $_SESSION['user']['user_id'];
$username = $_SESSION['user']['username'];
$review = trim($_POST['review']);

if ($review === '') {
    echo "Review cannot be empty.";
    exit;
}

$stmt = $pdo->prepare("INSERT INTO user_reviews (user_id, username, review) VALUES (?, ?, ?)");
$stmt->execute([$user_id, $username, $review]);

header('Location: option.php'); // or wherever the review form is
exit;
?>

<?php
require_once 'db.php';
session_start();

// ✅ Ensure user is logged in using the correct session structure
if (!isset($_SESSION['user']['user_id'])) {
    header('Location: login1.html');
    exit;
}

$user = $_SESSION['user'];
$userId = $user['user_id'];

// ✅ Handle delete request for this user's notifications only
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = (int)$_POST['delete_id'];
    $stmt = $pdo->prepare("UPDATE user_notifications SET is_deleted = 1 WHERE user_id = :user_id AND notification_id = :notif_id");
    $stmt->execute(['user_id' => $userId, 'notif_id' => $deleteId]);
}

// ✅ Mark unread notifications as read for this user
$pdo->prepare("UPDATE user_notifications SET is_read = 1 WHERE user_id = :user_id AND is_read = 0")
    ->execute(['user_id' => $userId]);

// ✅ Fetch user-specific notifications
$stmt = $pdo->prepare("
    SELECT n.id, n.message, n.created_at, un.is_read 
    FROM notifications n
    JOIN user_notifications un ON n.id = un.notification_id
    WHERE un.user_id = :user_id AND un.is_deleted = 0
    ORDER BY n.created_at DESC
");
$stmt->execute(['user_id' => $userId]);
$notifications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f8f8f8;
        }
        .card {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .notif {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .notif.unread {
            background-color: #fff6e5;
            font-weight: bold;
        }
        .notif:last-child {
            border-bottom: none;
        }
        .notif small {
            color: #888;
            display: block;
            margin-top: 5px;
        }
        .notif-content {
            flex-grow: 1;
        }
        .delete-btn {
            background: none;
            border: none;
            color: #d9534f;
            font-size: 16px;
            cursor: pointer;
        }
        .back-link {
            display: inline-block;
            margin-top: 15px;
            color: #ff6f3c;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>📢 Your Notifications</h2>

    <?php if (count($notifications) === 0): ?>
        <p>No notifications available.</p>
    <?php else: ?>
        <?php foreach ($notifications as $note): ?>
            <div class="notif <?php echo $note['is_read'] ? '' : 'unread'; ?>">
                <div class="notif-content">
                    <?php echo htmlspecialchars($note['message']); ?>
                    <small><?php echo htmlspecialchars($note['created_at']); ?></small>
                </div>
                <form method="post" onsubmit="return confirm('Delete this notification?');">
                    <input type="hidden" name="delete_id" value="<?php echo $note['id']; ?>">
                    <button class="delete-btn" title="Delete notification">🗑️</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <a class="back-link" href="javascript:history.back()">← Back</a>
</div>

</body>
</html>

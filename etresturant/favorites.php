<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']['user_id'])) {
    header('Location: ../login1.html');
    exit;
}

$user_id = $_SESSION['user']['user_id'];
$type = $_GET['type'] ?? 'food'; // default to 'food'

if ($type === 'drink') {
    // Drink favorites
    $stmt = $pdo->prepare("
        SELECT f.drink_id AS item_id, f.place_name, d.name AS item_name, p.price
        FROM drink_favorites f
        JOIN drink_items d ON f.drink_id = d.id
        LEFT JOIN drink_prices p ON f.drink_id = p.drink_id AND f.place_name = p.place_name
        WHERE f.user_id = ?
        ORDER BY f.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $favorites = $stmt->fetchAll();
    $title = "Your Favorite Drinks";
    $remove_action = "remove_drink_favorite.php";
$back_link = $type === 'drink' ? 'drink/drink.html' : 'food/res.html';
} else {
    // Food favorites (default)
    $stmt = $pdo->prepare("
        SELECT f.food_id AS item_id, f.place_name, fi.name AS item_name, fp.price
        FROM favorites f
        JOIN food_items fi ON f.food_id = fi.id
        LEFT JOIN food_prices fp ON f.food_id = fp.food_id AND f.place_name = fp.place_name
        WHERE f.user_id = ?
        ORDER BY f.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $favorites = $stmt->fetchAll();
    $title = "Your Favorite Foods";
    $remove_action = "remove_favorite.php";
    $back_link = "food/res.html";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title><?php echo htmlspecialchars($title); ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; padding: 20px; }
        h1 { color: #ff6f3c; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #ccc; text-align: left; }
        th { background-color: #ff6f3c; color: white; }
        a { color: #ff6f3c; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .no-favorites { margin-top: 20px; color: #555; }
        button { color: #ff6f3c; background: none; border: none; cursor: pointer; }
    </style>
</head>
<body>

<h1><?php echo htmlspecialchars($title); ?></h1>

<?php if (count($favorites) > 0): ?>
    <table>
        <thead>
            <tr>
                <th><?php echo ($type === 'drink') ? 'Drink' : 'Food'; ?></th>
                <th>Location</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($favorites as $fav): ?>
            <tr>
                <td><?php echo htmlspecialchars($fav['item_name']); ?></td>
                <td><?php echo htmlspecialchars($fav['place_name']); ?></td>
                <td><?php echo $fav['price'] !== null ? htmlspecialchars($fav['price']) . ' birr' : 'N/A'; ?></td>
                <td>
<a href="remove_favorite.php?type=<?php echo $type; ?>&item_id=<?php echo $fav['item_id']; ?>&place_name=<?php echo urlencode($fav['place_name']); ?>" onclick="return confirm('Remove this favorite?');">Remove</a>

                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="<?php echo $back_link; ?>">🔙 Back</a>
<?php else: ?>
    <p class="no-favorites">You have not added any favorite <?php echo $type === 'drink' ? 'drinks' : 'foods'; ?> yet.</p>
<?php endif; ?>

</body>
</html>

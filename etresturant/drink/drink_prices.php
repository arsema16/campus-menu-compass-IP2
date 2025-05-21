<?php
session_start();
require_once '../db.php';

$user_id = $_SESSION['user']['user_id'] ?? null;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid drink ID.");
}

$drink_id = intval($_GET['id']);

// Get drink name
$drink_stmt = $pdo->prepare("SELECT name FROM drink_items WHERE id = ?");
$drink_stmt->execute([$drink_id]);
$drink = $drink_stmt->fetch();

if (!$drink) {
    die("Drink not found.");
}

// Get prices
$price_stmt = $pdo->prepare("SELECT place_name, price FROM drink_prices WHERE drink_id = ?");
$price_stmt->execute([$drink_id]);
$prices = $price_stmt->fetchAll();

// Get user's favorite drink places for this drink
$fav_places = [];
if ($user_id) {
    $fav_stmt = $pdo->prepare("SELECT place_name FROM drink_favorites WHERE user_id = ? AND drink_id = ?");
    $fav_stmt->execute([$user_id, $drink_id]);
    $fav_places = $fav_stmt->fetchAll(PDO::FETCH_COLUMN);
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($drink['name']) ?> Prices</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            margin: 0;
            text-align: center;
        }
        .header {
            background-color: #ff6f3c;
            color: white;
            padding: 30px 20px;
            font-size: 28px;
            font-weight: bold;
            position: relative;
        }
        .subtitle {
            margin-top: 5px;
            font-size: 16px;
        }
        .notification-icon {
            position: absolute;
            right: 20px;
            top: 30px;
            font-size: 24px;
            color: white;
            text-decoration: none;
        }
        .notification-badge {
            position: absolute;
            top: 20px;
            right: 15px;
            background: red;
            color: white;
            border-radius: 50%;
            padding: 3px 6px;
            font-size: 12px;
        }
        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            width: 90%;
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 18px;
        }
        th, td {
            padding: 12px;
        }
        th {
            background-color: #ff6f3c;
            color: white;
        }
        td {
            border-top: 1px solid #eee;
            text-align: left;
        }
        .favorite-icon {
            cursor: pointer;
            font-size: 20px;
            margin-left: 10px;
            vertical-align: middle;
            user-select: none;
        }
        .back-btn {
            margin: 30px;
        }
        .back-btn a {
            background: #ff6f3c;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="header">
    <?= htmlspecialchars($drink['name']) ?> Prices
    <div class="subtitle">See where and for how much it's sold!</div>
</div>

<div class="card">
    <h2><?= htmlspecialchars($drink['name']) ?> Price List</h2>
    <table>
        <thead>
            <tr>
                <th>Location</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($prices): ?>
                <?php foreach ($prices as $row): 
                    $place = $row['place_name'];
                    $isFav = in_array($place, $fav_places);
                ?>
                    <tr>
                        <td><?= htmlspecialchars($place) ?></td>
                        <td>
                            <?= htmlspecialchars($row['price']) ?> birr
                            <?php if ($user_id): ?>
                                <span 
                                    class="favorite-icon" 
                                    data-drink-id="<?= $drink_id ?>" 
                                    data-place-name="<?= htmlspecialchars($place) ?>" 
                                    title="<?= $isFav ? 'Unfavorite' : 'Favorite' ?>"
                                    style="color: <?= $isFav ? 'red' : 'grey' ?>;">
                                    <i class="<?= $isFav ? 'ri-heart-fill' : 'ri-heart-line' ?>"></i>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="2">No prices found for this drink.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="back-btn">
    <a href="drink.html">⬅ Back to Menu</a>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
    $('.favorite-icon').click(function() {
        var span = $(this);
        var drink_id = span.data('drink-id');
        var place_name = span.data('place-name');

        $.post('toggle_drink_favorite.php', {
            drink_id: drink_id,
            place_name: place_name
        }, function(response) {
            if (response.status === 'success') {
                if (response.action === 'added') {
                    span.find('i').removeClass('ri-heart-line').addClass('ri-heart-fill');
                    span.css('color', 'red').attr('title', 'Unfavorite');
                } else if (response.action === 'removed') {
                    span.find('i').removeClass('ri-heart-fill').addClass('ri-heart-line');
                    span.css('color', 'grey').attr('title', 'Favorite');
                }
            } else {
                alert('Error: ' + response.message);
            }
        }, 'json');
    });
});
</script>

</body>
</html>

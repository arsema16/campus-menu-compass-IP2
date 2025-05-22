<?php
session_start();
require_once '../db.php';

$user_id = $_SESSION['user']['user_id'] ?? null;

$food_id = $_POST['food_id'] ?? null;
$place_name = $_POST['place_name'] ?? null;
$price = $_POST['price'] ?? null;

if ($food_id && $place_name && $price !== null) {
    // Update or insert into food_prices table
    $stmt = $pdo->prepare("UPDATE food_prices SET price = ? WHERE food_id = ? AND place_name = ?");
    $stmt->execute([$price, $food_id, $place_name]);

    if ($stmt->rowCount() == 0) {
        $insert_stmt = $pdo->prepare("INSERT INTO food_prices (food_id, place_name, price) VALUES (?, ?, ?)");
        $insert_stmt->execute([$food_id, $place_name, $price]);
    }

    // Get food name
    $food_stmt = $pdo->prepare("SELECT name FROM food_items WHERE id = ?");
    $food_stmt->execute([$food_id]);
    $food = $food_stmt->fetch();
    $food_name = $food['name'] ?? '';

    
} else {
    // If no POST update, just get food name from GET or POST id
    if (!$food_id) {
        $food_id = $_GET['id'] ?? null;
    }
    if ($food_id) {
        $food_stmt = $pdo->prepare("SELECT name FROM food_items WHERE id = ?");
        $food_stmt->execute([$food_id]);
        $food = $food_stmt->fetch();
        $food_name = $food['name'] ?? '';
    } else {
        $food_name = '';
    }
}

// Get all prices for this food
$price_stmt = $pdo->prepare("SELECT place_name, price FROM food_prices WHERE food_id = ?");
$price_stmt->execute([$food_id]);
$prices = $price_stmt->fetchAll();

// Get favorites for current user and this food
$fav_places = [];
if ($user_id && $food_id) {
    $fav_stmt = $pdo->prepare("SELECT place_name FROM favorites WHERE user_id = ? AND food_id = ?");
    $fav_stmt->execute([$user_id, $food_id]);
    $fav_places = $fav_stmt->fetchAll(PDO::FETCH_COLUMN);
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>price</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            text-align: center;
            margin: 0;
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
            width: 80%;
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
        a {
            color: blue;
            text-decoration: none;
        }
        .success-message {
            color: green;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .favorite-icon {
            cursor: pointer;
            font-size: 20px;
            margin-left: 10px;
            vertical-align: middle;
            user-select: none;
        }
    </style>
</head>
<body>

<div class="header">
    food price
    <div class="subtitle">Find the best price for your favorite food at different locations!</div>
</div>

<div class="card">
    <h2><?php echo htmlspecialchars($food_name); ?> Prices</h2>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $food_id && $place_name && $price !== null): ?>
        <p class="success-message">✅ Price updated successfully!</p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Location</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($prices as $row):
                $place = $row['place_name'];
                $isFav = in_array($place, $fav_places);
            ?>
            <tr>
                <td><?php echo htmlspecialchars($place); ?></td>
                <td>
                    <?php echo htmlspecialchars($row['price']); ?> birr

                    <?php if ($user_id): ?>
                        <span 
                            class="favorite-icon" 
                            data-food-id="<?php echo $food_id; ?>" 
                            data-place-name="<?php echo htmlspecialchars($place); ?>" 
                            title="<?php echo $isFav ? 'Unfavorite' : 'Favorite'; ?>"
                            style="color: <?= $isFav ? 'red' : 'grey' ?>;">
                            <i class="<?= $isFav ? 'ri-heart-fill' : 'ri-heart-line' ?>"></i>
                        </span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br />
    <a href="res.html">🔙 Back </a>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
    $('.favorite-icon').click(function() {
        var span = $(this);
        var food_id = span.data('food-id');
        var place_name = span.data('place-name');

        $.post('toggle_favorite.php', { food_id: food_id, place_name: place_name }, function(response) {
            if (response.status === 'success') {
                if (response.action === 'added') {
                    span.find('i').removeClass('ri-heart-line').addClass('ri-heart-fill');
                    span.css('color', 'red');
                    span.attr('title', 'Unfavorite');
                } else if (response.action === 'removed') {
                    span.find('i').removeClass('ri-heart-fill').addClass('ri-heart-line');
                    span.css('color', 'grey');
                    span.attr('title', 'Favorite');
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

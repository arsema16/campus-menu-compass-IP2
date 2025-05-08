<?php
// Define the drink menu items
$drinks = [
    ["name" => "Tea", "image" => "image/images.jpg"],
    ["name" => "Coffee", "image" => "image/image.jpg"],
    ["name" => "Macchiato", "image" => "image/mac.jpg"],
    ["name" => "Milk", "image" => "image/milk.jpg"],
    ["name" => "Papaya Juice", "image" => "image/papaya.jpg"],
    ["name" => "Avocado Juice", "image" => "image/avo.jpg"],
    ["name" => "Soft Drinks", "image" => "image/soft.jpg"],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drink Menu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Drink Menu</h1>
    </header>
    <main>
        <section class="drinks">
            <div class="drinks-grid">
                <?php foreach ($drinks as $drink) : ?>
                    <div class="drink-item">
                        <img src="<?php echo $drink['image']; ?>" alt="<?php echo $drink['name']; ?>">
                        <h4><?php echo $drink['name']; ?></h4>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 All Rights Reserved</p>
    </footer>
</body>
</html>

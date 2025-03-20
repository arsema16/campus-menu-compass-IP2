<?php
// Sample data to search through (replace with actual data)
$data = [
    ['title' => 'በያይነት', 'description' => 'A traditional dish with rich flavors and spices.'],
    ['title' => 'ተጋቢኖ', 'description' => 'A savory meal with a perfect blend of ingredients.'],
    ['title' => 'ምስር ወጥ', 'description' => 'A delicious lentil stew that is full of nutrients.'],
    ['title' => 'አተር ወጥ', 'description' => 'A spicy and aromatic stew made with meat and vegetables.'],
    ['title' => 'ሽሮ ወጥ', 'description' => 'A flavorful dish served with a mix of spices and herbs.'],
    ['title' => 'አልጫ ድንች', 'description' => 'Crispy and delicious fried potatoes.'],
    ['title' => 'ፍርፍር', 'description' => 'A fried dough snack, crispy and perfect for sharing.'],
    ['title' => 'ቲማቲም', 'description' => 'A fresh and vibrant tomato salad with spices.'],
    ['title' => 'ፓስታ', 'description' => 'Tasty pasta dishes served with different sauces.'],
    ['title' => 'መኮሮኒ', 'description' => 'Macaroni served with a creamy sauce and other ingredients.'],
    ['title' => 'ሩዝ', 'description' => 'Fluffy rice, a perfect side dish for any meal.'],
    ['title' => 'ሶያ', 'description' => 'A dish made with soybeans, rich in protein.'],
    ['title' => 'ፉል', 'description' => 'A popular Ethiopian dish made from fava beans.'],
    ['title' => 'እንቁላል', 'description' => 'Eggs prepared in various styles, a staple meal.'],
    ['title' => 'ቀይ ወጥ', 'description' => 'Spicy and flavorful red stew with meat and vegetables.'],
    ['title' => 'ምንቸት', 'description' => 'A hearty dish with a variety of spices and flavors.'],
    ['title' => 'ጥብስ', 'description' => 'A savory beef dish with rich flavors.'],
    ['title' => 'ዱለት', 'description' => 'A dish featuring minced meat and various seasonings.'],
    ['title' => 'ዶናት', 'description' => 'A sweet, deep-fried dough pastry.'],
    ['title' => 'ፍላፍል', 'description' => 'A delightful and crispy snack.'],
    ['title' => 'ሳንቡሳ', 'description' => 'Fried pastry filled with meat or lentils.'],
    ['title' => 'እርጥብ', 'description' => 'A traditional Ethiopian snack with a savory taste.']
];

// Initialize result variable
$results = [];

// Check if the search query is set and filter data
if (isset($_POST['search'])) {
    $searchQuery = strtolower(trim($_POST['search']));
    
    // Filter the results based on search query
    foreach ($data as $item) {
        if (strpos(strtolower($item['title']), $searchQuery) !== false || 
            strpos(strtolower($item['description']), $searchQuery) !== false) {
            $results[] = $item;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            background-color: #333;
            padding: 20px;
            color: white;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
        }

        .search-container {
            margin: 20px;
        }

        .search-container input {
            padding: 10px;
            width: 300px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .results-container {
            margin-top: 20px;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
        }

        .result-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .result-item:last-child {
            border-bottom: none;
        }

        .result-item strong {
            font-size: 18px;
        }

        .result-item p {
            font-size: 16px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <div class="logo">
            <img src="img/logo.jpg" alt="Logo" />
        </div>
        <nav class="nav-links">
            <a href="res.php">Home</a>
            <a href="contact.php">Contact</a>
        </nav>
    </header>

    <!-- Search Form -->
    <div class="search-container">
        <form method="POST">
            <input type="text" name="search" placeholder="Search..." value="<?php echo isset($searchQuery) ? $searchQuery : ''; ?>" />
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Search Results -->
    <div class="results-container">
        <?php
        if (isset($_POST['search'])) {
            if (count($results) > 0) {
                foreach ($results as $result) {
                    echo "<div class='result-item'>";
                    echo "<strong>" . htmlspecialchars($result['title']) . "</strong>";
                    echo "<p>" . htmlspecialchars($result['description']) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No results found.</p>";
            }
        }
        ?>
    </div>
</body>
</html>

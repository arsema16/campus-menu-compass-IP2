<?php
// Sample data
$data = [
    ['title' => 'Tea', 'description' => 'A refreshing drink made from steeped tea leaves.'],
    ['title' => 'Coffee', 'description' => 'A rich, energizing beverage made from brewed coffee beans.'],
    ['title' => 'Macchiato', 'description' => 'A strong espresso with a touch of foamed milk.'],
    ['title' => 'Milk', 'description' => 'A creamy beverage, perfect for any time of day.'],
    ['title' => 'Papaya Juice', 'description' => 'A tropical juice made from fresh papayas.'],
    ['title' => 'Avocado Juice', 'description' => 'A creamy and healthy drink made from ripe avocados.'],
    ['title' => 'Soft Drinks', 'description' => 'Carbonated beverages for a refreshing taste.'],
];

$searchQuery = "";
$filteredResults = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"])) {
    $searchQuery = strtolower($_POST["search"]);

    // Filter results based on the search query
    $filteredResults = array_filter($data, function($item) use ($searchQuery) {
        return (strpos(strtolower($item['title']), $searchQuery) !== false || 
                strpos(strtolower($item['description']), $searchQuery) !== false);
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Example</title>
    <style>
        /* Your CSS here */
        #search-bar {
            position: absolute;
            top: 50px;
            right: 10px;
            background-color: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 10px;
            width: 200px;
            border-radius: 5px;
            z-index: 1000;
        }
        
        #search-input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        #search-results {
            margin-top: 10px;
            max-height: 200px;
            overflow-y: auto;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }

        #search-results div {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        #search-results div:hover {
            background-color: #f7f7f7;
        }

        #search-icon {
            font-size: 24px;
            cursor: pointer;
            display: inline-block;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div id="search-icon">🔍</div>

    <div id="search-bar" style="display: none;">
        <form method="POST">
            <input type="text" name="search" id="search-input" placeholder="Search..." value="<?php echo htmlspecialchars($searchQuery); ?>" />
            <button type="submit">Search</button>
        </form>
        <div id="search-results">
            <?php if (!empty($filteredResults)): ?>
                <?php foreach ($filteredResults as $result): ?>
                    <div>
                        <strong><?php echo htmlspecialchars($result['title']); ?></strong>
                        <p><?php echo htmlspecialchars($result['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php elseif ($searchQuery): ?>
                <p>No results found.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Show the search bar when the search icon is clicked
        document.getElementById('search-icon').onclick = function(event) {
            event.preventDefault();
            document.getElementById('search-bar').style.display = 'flex';
            document.getElementById('search-input').focus();
        };

        // Optional: Close search bar when clicking outside of it
        window.onclick = function(event) {
            if (!document.getElementById('search-bar').contains(event.target) && event.target !== document.getElementById('search-icon')) {
                document.getElementById('search-bar').style.display = 'none';
            }
        };
    </script>
</body>
</html>

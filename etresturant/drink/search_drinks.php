<?php
// search_drinks.php
header('Content-Type: application/json');

$host = 'localhost';
$db = 'menu_web';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = isset($_GET['query']) ? $_GET['query'] : '';

    $stmt = $pdo->prepare("
    SELECT drink_items.name, drink_prices.place_name, drink_prices.price
    FROM drink_prices
    JOIN drink_items ON drink_prices.drink_id = drink_items.id
    WHERE drink_items.name LIKE :query OR drink_prices.place_name LIKE :query
");

$stmt->execute(['query' => "%$query%"]);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

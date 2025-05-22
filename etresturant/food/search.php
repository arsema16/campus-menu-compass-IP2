<?php
// search.php
header('Content-Type: application/json');

$host = 'localhost';
$db = 'menu_web';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = isset($_GET['query']) ? trim($_GET['query']) : '';

    if ($query === '') {
        echo json_encode([]);
        exit;
    }

    $sql = "
        SELECT fi.id, fi.name, fp.place_name, fp.price
        FROM food_prices fp
        JOIN food_items fi ON fp.food_id = fi.id
        WHERE fi.name LIKE :query OR fp.place_name LIKE :query
        LIMIT 50
    ";

    $stmt = $pdo->prepare($sql);
    $likeQuery = "%$query%";
    $stmt->bindParam(':query', $likeQuery, PDO::PARAM_STR);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>

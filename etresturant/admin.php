<?php
session_start();
require_once 'db.php'; // Should define $pdo as the PDO connection

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login1.html");
    exit;
}

$user = $_SESSION['user'];
$username = htmlspecialchars($user['username']);
$role = strtolower($user['role']);
$place = $user['place'] ?? '';

$updateMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_type'])) {
    $updateType = $_POST['update_type']; // 'food' or 'drink'
    $itemId = $_POST['item_id'] ?? null;
    $newPrice = $_POST['new_price'] ?? null;

    if ($itemId && $newPrice !== null) {
        $newPrice = floatval($newPrice);

        try {
    if ($updateType === 'food') {
        // Fetch food name and place before updating
        $stmt = $pdo->prepare("
            SELECT fi.name AS food_name, fp.place_name 
            FROM food_prices fp 
            JOIN food_items fi ON fp.food_id = fi.id 
            WHERE fp.id = :id
        ");
        $stmt->execute(['id' => $itemId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) throw new Exception("Food item not found.");

        $foodName = $row['food_name'];
        $placeName = $row['place_name'];

        $sql = ($role === 'super_admin')
            ? "UPDATE food_prices SET price = :price, updated_at = NOW() WHERE id = :id"
            : "UPDATE food_prices SET price = :price, updated_at = NOW() WHERE id = :id AND place_name = :place";
        $params = ['price' => $newPrice, 'id' => $itemId];
        if ($role !== 'super_admin') $params['place'] = $place;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $notifMsg = "Food price for '$foodName' updated to $newPrice at place '$placeName'.";

    } elseif ($updateType === 'drink') {
        // Fetch drink name and place before updating
        $stmt = $pdo->prepare("
            SELECT di.name AS drink_name, dp.place_name 
            FROM drink_prices dp 
            JOIN drink_items di ON dp.drink_id = di.id 
            WHERE dp.id = :id
        ");
        $stmt->execute(['id' => $itemId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) throw new Exception("Drink item not found.");

        $drinkName = $row['drink_name'];
        $placeName = $row['place_name'];

        $sql = ($role === 'super_admin')
            ? "UPDATE drink_prices SET price = :price, updated_at = NOW() WHERE id = :id"
            : "UPDATE drink_prices SET price = :price, updated_at = NOW() WHERE id = :id AND place_name = :place";
        $params = ['price' => $newPrice, 'id' => $itemId];
        if ($role !== 'super_admin') $params['place'] = $place;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $notifMsg = "Drink price for '$drinkName' updated to $newPrice at place '$placeName'.";

    } else {
        throw new Exception("Invalid update type.");
    }

    // Insert notification
    // Step 1: Insert notification
$stmtNotif = $pdo->prepare("INSERT INTO notifications (message) VALUES (:message)");
$stmtNotif->execute(['message' => $notifMsg]);
$notifId = $pdo->lastInsertId();

// Step 2: Link it to the current user
$stmtUserNotif = $pdo->prepare("INSERT INTO user_notifications (user_id, notification_id) VALUES (:user_id, :notification_id)");
$stmtUserNotif->execute(['user_id' => $user['user_id'], 'notification_id' => $notifId]);


    $updateMessage = ucfirst($updateType) . " price updated successfully.";

} catch (PDOException $e) {
    $updateMessage = "Update failed: " . $e->getMessage();
} catch (Exception $e) {
    $updateMessage = "Error: " . $e->getMessage();
}


    } else {
        $updateMessage = "Invalid input for update.";
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        switch ($_POST['action']) {
            case 'add_food':
                $newFoodName = trim($_POST['new_food_name']);
                $newFoodPrice = floatval($_POST['new_food_price']);
                $placeForInsert = ($role === 'super_admin' && !empty($_POST['place'])) ? trim($_POST['place']) : $place;

                // Insert or get food item
                $stmt = $pdo->prepare("INSERT INTO food_items (name) VALUES (:name) ON DUPLICATE KEY UPDATE name = name");
                $stmt->execute(['name' => $newFoodName]);
                $stmt = $pdo->prepare("SELECT id FROM food_items WHERE name = :name");
                $stmt->execute(['name' => $newFoodName]);
                $food_id = $stmt->fetchColumn();

                // Insert price
                $stmt = $pdo->prepare("INSERT INTO food_prices (food_id, price, place_name, updated_at) VALUES (:food_id, :price, :place, NOW())");
                $stmt->execute(['food_id' => $food_id, 'price' => $newFoodPrice, 'place' => $placeForInsert]);

                $updateMessage = "Food item added successfully.";

                // Insert notification
                $notifMsg = "Added food item '$newFoodName' with price $newFoodPrice at place '$placeForInsert'.";
                // Step 1: Insert notification
$stmtNotif = $pdo->prepare("INSERT INTO notifications (message) VALUES (:message)");
$stmtNotif->execute(['message' => $notifMsg]);
$notifId = $pdo->lastInsertId();

// Step 2: Link it to the current user
$stmtUserNotif = $pdo->prepare("INSERT INTO user_notifications (user_id, notification_id) VALUES (:user_id, :notification_id)");
$stmtUserNotif->execute(['user_id' => $user['user_id'], 'notification_id' => $notifId]);

                break;

            case 'delete_food':
    $id = (int)$_POST['item_id'];

    // Fetch the food name and place before deleting
    $stmt = $pdo->prepare("SELECT fi.name, fp.place_name FROM food_prices fp JOIN food_items fi ON fp.food_id = fi.id WHERE fp.id = :id");
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $foodName = $row['name'];
        $placeName = $row['place_name'];

        $sql = ($role === 'super_admin')
            ? "DELETE FROM food_prices WHERE id = :id"
            : "DELETE FROM food_prices WHERE id = :id AND place_name = :place";

        $stmt = $pdo->prepare($sql);
        $params = ['id' => $id];
        if ($role !== 'super_admin') $params['place'] = $place;
        $stmt->execute($params);

        $updateMessage = "Food item deleted successfully.";

        // Insert notification with food name and place
        $notifMsg = "Deleted food item '$foodName' at place '$placeName'.";
        // Step 1: Insert notification
$stmtNotif = $pdo->prepare("INSERT INTO notifications (message) VALUES (:message)");
$stmtNotif->execute(['message' => $notifMsg]);
$notifId = $pdo->lastInsertId();

// Step 2: Link it to the current user
$stmtUserNotif = $pdo->prepare("INSERT INTO user_notifications (user_id, notification_id) VALUES (:user_id, :notification_id)");
$stmtUserNotif->execute(['user_id' => $user['user_id'], 'notification_id' => $notifId]);

    } else {
        $updateMessage = "Food item not found.";
    }
    break;

            case 'add_drink':
                $newDrinkName = trim($_POST['new_drink_name']);
                $newDrinkPrice = floatval($_POST['new_drink_price']);
                $placeForInsert = ($role === 'super_admin' && !empty($_POST['place'])) ? trim($_POST['place']) : $place;

                $stmt = $pdo->prepare("INSERT INTO drink_items (name) VALUES (:name) ON DUPLICATE KEY UPDATE name = name");
                $stmt->execute(['name' => $newDrinkName]);
                $stmt = $pdo->prepare("SELECT id FROM drink_items WHERE name = :name");
                $stmt->execute(['name' => $newDrinkName]);
                $drink_id = $stmt->fetchColumn();

                $stmt = $pdo->prepare("INSERT INTO drink_prices (drink_id, price, place_name, updated_at) VALUES (:drink_id, :price, :place, NOW())");
                $stmt->execute(['drink_id' => $drink_id, 'price' => $newDrinkPrice, 'place' => $placeForInsert]);

                $updateMessage = "Drink item added successfully.";

                // Insert notification
                $notifMsg = "Added drink item '$newDrinkName' with price $newDrinkPrice at place '$placeForInsert'.";
                // Step 1: Insert notification
$stmtNotif = $pdo->prepare("INSERT INTO notifications (message) VALUES (:message)");
$stmtNotif->execute(['message' => $notifMsg]);
$notifId = $pdo->lastInsertId();

// Step 2: Link it to the current user
$stmtUserNotif = $pdo->prepare("INSERT INTO user_notifications (user_id, notification_id) VALUES (:user_id, :notification_id)");
$stmtUserNotif->execute(['user_id' => $user['user_id'], 'notification_id' => $notifId]);

                break;

            case 'delete_drink':
    $id = (int)$_POST['item_id'];

    // Fetch the drink name and place before deleting
    $stmt = $pdo->prepare("SELECT di.name, dp.place_name FROM drink_prices dp JOIN drink_items di ON dp.drink_id = di.id WHERE dp.id = :id");
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $drinkName = $row['name'];
        $placeName = $row['place_name'];

        $sql = ($role === 'super_admin')
            ? "DELETE FROM drink_prices WHERE id = :id"
            : "DELETE FROM drink_prices WHERE id = :id AND place_name = :place";

        $stmt = $pdo->prepare($sql);
        $params = ['id' => $id];
        if ($role !== 'super_admin') $params['place'] = $place;
        $stmt->execute($params);

        $updateMessage = "Drink item deleted successfully.";

        // Insert notification with drink name and place
        $notifMsg = "Deleted drink item '$drinkName' at place '$placeName'.";
        // Step 1: Insert notification
$stmtNotif = $pdo->prepare("INSERT INTO notifications (message) VALUES (:message)");
$stmtNotif->execute(['message' => $notifMsg]);
$notifId = $pdo->lastInsertId();

// Step 2: Link it to the current user
$stmtUserNotif = $pdo->prepare("INSERT INTO user_notifications (user_id, notification_id) VALUES (:user_id, :notification_id)");
$stmtUserNotif->execute(['user_id' => $user['user_id'], 'notification_id' => $notifId]);

    } else {
        $updateMessage = "Drink item not found.";
    }
    break;

        }
    } catch (PDOException $e) {
        $updateMessage = "Action failed: " . $e->getMessage();
    }
}

// Fetch data
try {
    if ($role === 'super_admin') {
        $foodItems = $pdo->query("
            SELECT fp.id, fi.name AS food_name, fp.price, fp.place_name, fp.updated_at
            FROM food_prices fp
            JOIN food_items fi ON fp.food_id = fi.id
            ORDER BY fp.place_name, fi.name
        ")->fetchAll(PDO::FETCH_ASSOC);

        $drinkItems = $pdo->query("
            SELECT dp.id, di.name AS drink_name, dp.price, dp.place_name, dp.updated_at
            FROM drink_prices dp
            JOIN drink_items di ON dp.drink_id = di.id
            ORDER BY dp.place_name, di.name
        ")->fetchAll(PDO::FETCH_ASSOC);

        $adminUsers = $pdo->query("SELECT username, email, role, place FROM users ORDER BY username")->fetchAll(PDO::FETCH_ASSOC);
    $limit = 10; // messages per page
    $page = isset($_GET['contact_page']) ? max(1, intval($_GET['contact_page'])) : 1;
    $offset = ($page - 1) * $limit;

    // Count total messages
    $totalMessages = $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
    $totalPages = ceil($totalMessages / $limit);

    // Fetch paginated messages
    $stmt = $pdo->prepare("SELECT name, email, message, submitted_at FROM contact_messages ORDER BY submitted_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $contactMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $pdo->prepare("
            SELECT fp.id, fi.name AS food_name, fp.price, fp.place_name, fp.updated_at
            FROM food_prices fp
            JOIN food_items fi ON fp.food_id = fi.id
            WHERE fp.place_name = :place
            ORDER BY fi.name
        ");
        $stmt->execute(['place' => $place]);
        $foodItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("
            SELECT dp.id, di.name AS drink_name, dp.price, dp.place_name, dp.updated_at
            FROM drink_prices dp
            JOIN drink_items di ON dp.drink_id = di.id
            WHERE dp.place_name = :place
            ORDER BY di.name
        ");
        $stmt->execute(['place' => $place]);
        $drinkItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Panel</title>
<style>
  body {
    font-family: Arial, sans-serif;
    margin: 0; padding: 0;
  }
  header {
    background: #003366;
    color: white;
    padding: 10px 20px;
    display: flex; justify-content: space-between; align-items: center;
  }
  header h1 {
    margin: 0;
  }
  header .logout-btn {
    background: #e60000;
    color: white;
    border: none;
    padding: 8px 15px;
    cursor: pointer;
    font-size: 14px;
    border-radius: 3px;
  }
  nav {
    background: #0055aa;
    padding: 10px 20px;
    display: flex;
    gap: 15px;
  }
  nav button {
    background: #0077dd;
    color: white;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    font-size: 15px;
    border-radius: 3px;
    transition: background 0.3s;
  }
  nav button:hover, nav button.active {
    background: #004499;
  }
  section {
    padding: 20px;
  }
  table {
    border-collapse: collapse;
    width: 100%;
    margin-bottom: 20px;
  }
  table, th, td {
    border: 1px solid #aaa;
  }
  th, td {
    padding: 8px 12px;
    text-align: left;
  }
  input[type="number"] {
    width: 80px;
  }
  .message {
    margin-bottom: 20px;
    color: green;
  }
  form.inline-form {
    display: inline-block;
    margin: 0;
  }
  .btn-delete {
    background: #cc3300;
    color: white;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    border-radius: 3px;
  }
  .btn-add {
    background: #009900;
    color: white;
    border: none;
    padding: 7px 15px;
    cursor: pointer;
    border-radius: 3px;
  }
  label {
    display: inline-block;
    width: 100px;
  }
  input[type="text"], input[type="email"], select {
    padding: 5px;
    width: 200px;
  }
  .admin-table {
    max-width: 700px;
  }
  .btn-edit {
  background: #0066cc;
  color: white;
  border: none;
  padding: 5px 10px;
  margin-left: 5px;
  cursor: pointer;
  border-radius: 3px;
}
.pagination {
  margin-top: 20px;
}
.pagination a {
  padding: 6px 12px;
  margin: 2px;
  text-decoration: none;
  background-color: #eee;
  color: #333;
  border-radius: 4px;
}
.pagination a.active {
  background-color: #333;
  color: #fff;
}


</style>
<script>
function showSection(sectionId) {
  document.querySelectorAll('section').forEach(sec => {
    sec.style.display = 'none';
  });
  const btns = document.querySelectorAll('nav button');
  btns.forEach(btn => {
    btn.classList.remove('active');
  });
  document.getElementById(sectionId).style.display = 'block';
  document.querySelector(`nav button[data-section="${sectionId}"]`).classList.add('active');
}

// Confirm before deleting
function confirmDelete(itemType) {
  return confirm(`Are you sure you want to delete this ${itemType}?`);
}

window.onload = function() {
  showSection('food');
};
</script>
</head>
<body>

<header>
  <h1>Admin Panel</h1>
  <div>
    Logged in as <strong><?php echo $username; ?></strong> (<?php echo $role; ?>)
    <form style="display:inline" action="logout.html" method="post">
      <button class="logout-btn" type="submit">Logout</button>
    </form>
  </div>
</header>

<nav>
  <button data-section="food" onclick="showSection('food')">Food Prices</button>
  <button data-section="drink" onclick="showSection('drink')">Drink Prices</button>
  <?php if ($role === 'super_admin'): ?>
  <button data-section="admins" onclick="showSection('admins')">Manage Admins</button>
    <button data-section="contact" onclick="showSection('contact')">Contact Messages</button> 
  <?php endif; ?>
</nav>

<main>
  <?php if ($updateMessage): ?>
    <p class="message"><?php echo htmlspecialchars($updateMessage); ?></p>
  <?php endif; ?>

  <!-- Food Prices Section -->
  <section id="food" style="display:none;">
    <h2>Food Prices</h2>
    <table>
      <thead>
        <tr>
          <th>Food Name</th>
          <th>Price</th>
          <?php if ($role === 'super_admin'): ?>
            <th>Place</th>
          <?php endif; ?>
          <th>Updated At</th>
          <th>Delete</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($foodItems as $item): ?>
          <tr>
            <td><?php echo htmlspecialchars($item['food_name']); ?></td>
            <td>
              <form method="post" class="inline-form" action="admin.php">
                <input type="hidden" name="update_type" value="food" />
                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>" />
<input type="number" step="1" min="0" name="new_price" value="<?php echo $item['price']; ?>" />
<button type="submit" class="btn-edit">Edit</button>
              </form>
            </td>
            <?php if ($role === 'super_admin'): ?>
              <td><?php echo htmlspecialchars($item['place_name']); ?></td>
            <?php endif; ?>
            <td><?php echo htmlspecialchars($item['updated_at']); ?></td>
            <td>
              <form method="post" onsubmit="return confirmDelete('food item');" class="inline-form" action="admin.php">
                <input type="hidden" name="action" value="delete_food" />
                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>" />
                <button type="submit" class="btn-delete">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <h3>Add New Food Item</h3>
    <form method="post" action="admin.php">
      <input type="hidden" name="action" value="add_food" />
      <label for="new_food_name">Name:</label>
      <input type="text" id="new_food_name" name="new_food_name" required />
      <label for="new_food_price">Price:</label>
      <input type="number" id="new_food_price" name="new_food_price" step="0.01" min="0" required />
      <?php if ($role === 'super_admin'): ?>
        <label for="place">Place:</label>
        <input type="text" id="place" name="place" placeholder="Enter place name" required />
      <?php endif; ?>
      <button type="submit" class="btn-add">Add Food</button>
    </form>
  </section>

  <!-- Drink Prices Section -->
  <section id="drink" style="display:none;">
    <h2>Drink Prices</h2>
    <table>
      <thead>
        <tr>
          <th>Drink Name</th>
          <th>Price</th>
          <?php if ($role === 'super_admin'): ?>
            <th>Place</th>
          <?php endif; ?>
          <th>Updated At</th>
          <th>Delete</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($drinkItems as $item): ?>
          <tr>
            <td><?php echo htmlspecialchars($item['drink_name']); ?></td>
            <td>
              <form method="post" class="inline-form" action="admin.php">
                <input type="hidden" name="update_type" value="drink" />
                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>" />
<input type="number" step="1" min="0" name="new_price" value="<?php echo $item['price']; ?>" />
<button type="submit" class="btn-edit">Edit</button>
              </form>
            </td>
            <?php if ($role === 'super_admin'): ?>
              <td><?php echo htmlspecialchars($item['place_name']); ?></td>
            <?php endif; ?>
            <td><?php echo htmlspecialchars($item['updated_at']); ?></td>
            <td>
              <form method="post" onsubmit="return confirmDelete('drink item');" class="inline-form" action="admin.php">
                <input type="hidden" name="action" value="delete_drink" />
                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>" />
                <button type="submit" class="btn-delete">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <h3>Add New Drink Item</h3>
    <form method="post" action="admin.php">
      <input type="hidden" name="action" value="add_drink" />
      <label for="new_drink_name">Name:</label>
      <input type="text" id="new_drink_name" name="new_drink_name" required />
      <label for="new_drink_price">Price:</label>
      <input type="number" id="new_drink_price" name="new_drink_price" step="0.01" min="0" required />
      <?php if ($role === 'super_admin'): ?>
        <label for="place">Place:</label>
        <input type="text" id="place" name="place" placeholder="Enter place name" required />
      <?php endif; ?>
      <button type="submit" class="btn-add">Add Drink</button>
    </form>
  </section>

  <!-- Admin Management Section (only super_admin) -->
  <?php if ($role === 'super_admin'): ?>
  <section id="admins" style="display:none;">
    <h2>Manage Admin Users</h2>
    <table class="admin-table">
      <thead>
        <tr>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
          <th>Place</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($adminUsers as $admin): ?>
          <tr>
            <td><?php echo htmlspecialchars($admin['username']); ?></td>
            <td><?php echo htmlspecialchars($admin['email']); ?></td>
            <td><?php echo htmlspecialchars($admin['role']); ?></td>
            <td><?php echo htmlspecialchars($admin['place']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p><em>Admin management functionality (add/update/delete admins) can be implemented here as needed.</em></p>
  </section>
  <?php endif; ?>

  <?php if ($role === 'super_admin'): ?>
<section id="contact" style="display:none;">
  <h2>Contact Messages</h2>
  <?php if (empty($contactMessages)): ?>
    <p>No contact messages found.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Message</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($contactMessages as $msg): ?>
          <tr>
            <td><?php echo htmlspecialchars($msg['name']); ?></td>
            <td><?php echo htmlspecialchars($msg['email']); ?></td>
            <td><?php echo nl2br(htmlspecialchars($msg['message'])); ?></td>
            <td><?php echo htmlspecialchars($msg['submitted_at']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?contact_page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
          <?php echo $i; ?>
        </a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</section>
<?php endif; ?>

</main>
<script>
  // Auto-show contact section if URL contains ?contact_page
  window.addEventListener("DOMContentLoaded", function () {
    if (window.location.href.includes("contact_page")) {
      showSection("contact");
    }
  });
</script>

</body>
</html>

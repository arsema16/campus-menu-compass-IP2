<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch user data from the database
$query = "SELECT * FROM users WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found!";
    exit();
}

// Update user profile if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];

    // Update user data in the database
    $query = "UPDATE users SET first_name = :first_name, last_name = :last_name WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':id', $user_id);

    if ($stmt->execute()) {
        echo "Profile updated successfully!";
    } else {
        echo "Error: Could not update profile.";
    }
}
?>

<h1>Welcome, <?php echo htmlspecialchars($user['username']); ?></h1>
<p>Email: <?php echo htmlspecialchars($user['email']); ?></p>

<form method="POST" action="">
    First Name: <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>"><br>
    Last Name: <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>"><br>
    <button type="submit">Update Profile</button>
</form>

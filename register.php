<?php
// Database connection
$host = "localhost";
$dbname = "user_profiles";
$username = "root"; // Change if needed
$password = ""; // Change if needed

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password

    // Ensure column names match the database
    $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
    $stmt = $conn->prepare($sql);
    
    try {
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $password
        ]);
        $message = "✅ Registration successful!";
    } catch (PDOException $e) {
        $message = "❌ Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
</head>
<body>

    <h2>Register</h2>

    <!-- Show success/error message -->
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <label>Username:</label>
        <input type="text" name="username" required><br><br>

        <label>Email:</label>
        <input type="email" name="email" required><br><br>

        <label>Password:</label>
        <input type="password" name="password" required><br><br>

        <button type="submit">Register</button>
    </form>

</body>
</html>

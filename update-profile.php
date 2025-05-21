<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login1.html");
    exit;
}

$user = $_SESSION['user'];
$userId = $user['user_id']; // or whatever key you use for user ID

$host = 'localhost';
$db = 'menu_web';
$userDB = 'root';
$passDB = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $userDB, $passDB);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $username = $_POST['username'];
    $email = $_POST['email'];

    $profilePicPath = null;
    if (!empty($_FILES['profile_pic']['tmp_name'])) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES["profile_pic"]["name"]);
        $targetFile = $targetDir . time() . "_" . $fileName;

        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFile)) {
            $profilePicPath = $targetFile;
        }
    }

    // Build SQL query
    if ($profilePicPath) {
        $sql = "UPDATE users SET username = :username, email = :email, profile_pic = :profile_pic WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':profile_pic' => $profilePicPath,
            ':id' => $userId
        ]);
    } else {
        $sql = "UPDATE users SET username = :username, email = :email WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':id' => $userId
        ]);
    }

    // Update session data
    $_SESSION['user']['username'] = $username;
    $_SESSION['user']['email'] = $email;
    if ($profilePicPath) {
        $_SESSION['user']['profile_pic'] = $profilePicPath;
    }

    header("Location: profile.php");
    exit;

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>

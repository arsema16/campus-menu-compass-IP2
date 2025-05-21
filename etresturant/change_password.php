<?php
session_start();
include('db.php');

$message = '';

if (!isset($_SESSION['user'])) {
    header('Location: login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];

    $userId = $_SESSION['user']['user_id'];

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($currentPassword, $user['password'])) {
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $updateStmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        $updateStmt->bindParam(':password', $hashedNewPassword);
        $updateStmt->bindParam(':id', $userId);

        if ($updateStmt->execute()) {
            $message = "✅ Password changed successfully.";
        } else {
            $message = "❌ Error changing password.";
        }
    } else {
        $message = "❌ Incorrect current password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f5f7fa;
      margin: 0;
      padding: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }

    .profile-container {
      background: white;
      padding: 30px 25px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      max-width: 400px;
      width: 100%;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }

    input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-top: 12px;
      margin-bottom: 15px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    .btn {
      width: 100%;
      background-color: #1a73e8;
      color: white;
      padding: 10px;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .btn:hover {
      background-color: #0c59c3;
    }

    .message {
      text-align: center;
      margin-top: 15px;
      font-weight: bold;
      color: #d8000c;
    }

    .message.success {
      color: green;
    }

    @media (max-width: 500px) {
      .profile-container {
        padding: 20px;
      }
    }
  </style>
</head>
<body>

<div class="profile-container">
  <h2>Change Password</h2>
  <form action="change-password.php" method="POST">
    <input type="password" name="current_password" placeholder="Current Password" required>
    <input type="password" name="new_password" placeholder="New Password" required>
    <button type="submit" class="btn">Change Password</button>
  </form>

  <?php if (!empty($message)): ?>
    <div class="message <?= strpos($message, 'successfully') !== false ? 'success' : '' ?>">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>
</div>

</body>
</html>

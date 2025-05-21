<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

include('db.php');

// Dummy last login time logic (replace with real DB value if tracked)
$lastLogin = isset($_SESSION['last_login']) ? $_SESSION['last_login'] : date('Y-m-d H:i:s');
$type = $_GET['type'] ?? null; // Get 'type' from the URL, or null if not set
$back_link = $type === 'drink' ? 'drink/drink.html' : 'food/res.html';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    $userNotLoggedIn = true;
} else {
    $user = $_SESSION['user'];
    $userFirstName = explode(' ', $user['username'])[0];
    $userNotLoggedIn = false;
}

// Flash message logic (optional)
$flashMessage = '';
if (isset($_SESSION['flash'])) {
    $flashMessage = $_SESSION['flash'];
    unset($_SESSION['flash']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Profile Page</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #ffecd2, #fcb69f);
      margin: 0;
      padding: 0;
    }

    .profile-container {
      max-width: 500px;
      margin: 50px auto;
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .profile-img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      background: #d92222;
      color: white;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 48px;
      margin: 0 auto 15px;
    }

    .profile-header {
      text-align: center;
    }

    .profile-header h2 {
      margin: 10px 0 5px;
      font-size: 24px;
      color: #333;
    }

    .profile-info {
      margin-top: 20px;
      text-align: left;
      padding: 0 10px;
    }

    .profile-info p {
      font-size: 16px;
      margin: 10px 0;
    }

    .btn {
      background: #d92222;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 8px;
      width: 100%;
      margin-top: 15px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s;
      text-align: center;
      text-decoration: none;
      display: inline-block;
    }

    .btn:hover {
      background: #b71c1c;
    }

    .error-message {
      text-align: center;
      color: red;
      font-weight: bold;
    }

    .flash {
      background-color: #e6ffee;
      border: 1px solid #2ecc71;
      padding: 12px;
      margin-bottom: 20px;
      text-align: center;
      border-radius: 5px;
      color: #27ae60;
      font-weight: bold;
    }

    a.btn {
      display: block;
    }
  </style>
</head>
<body>

<div class="profile-container">
    <?php if ($userNotLoggedIn): ?>
        <div class="error-message">
            <p>You are not logged in. Please <a href="login1.html">log in</a>.</p>
        </div>
    <?php else: ?>
        <?php if ($flashMessage): ?>
            <div class="flash"><?php echo htmlspecialchars($flashMessage); ?></div>
        <?php endif; ?>

        <div class="profile-header">
            <div class="profile-img">
                <?php echo strtoupper(htmlspecialchars($userFirstName[0])); ?>
            </div>
            <h2><?php echo htmlspecialchars($user['username']); ?></h2>
        </div>

        <div class="profile-info">
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
            <p><strong>Last Login:</strong> <?php echo htmlspecialchars($lastLogin); ?></p>
        </div>

        <a href="edit-profile.php" class="btn">Edit Profile</a>
        <a href="change_password.php" class="btn">Change Password</a>
        <a href="logout.html" class="btn">Logout</a>
        <a href="<?php echo $back_link; ?>">🔙 Back</a>
    <?php endif; ?>
</div>

</body>
</html>

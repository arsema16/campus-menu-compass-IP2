<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login1.html");
    exit;
}
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef;
            padding: 20px;
        }

        .edit-form {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="text"], input[type="email"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #999;
        }

        .btn {
            background-color: #1a73e8;
            color: white;
            padding: 10px;
            width: 100%;
            border: none;
            margin-top: 20px;
            cursor: pointer;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #0c59c3;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #1a73e8;
        }
    </style>
</head>
<body>

<div class="edit-form">
    <h2>Edit Profile</h2>
    <form action="update-profile.php" method="POST" enctype="multipart/form-data">
        <label for="username">Name</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

        <label for="email">Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label for="profile_pic">Profile Picture</label>
        <input type="file" name="profile_pic" accept="image/*">

        <button type="submit" class="btn">Update Profile</button>
    </form>

    <a href="profile.php">← Back to Profile</a>
</div>

</body>
</html>

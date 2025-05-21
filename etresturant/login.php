<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = strtolower(trim($_POST['role'] ?? ''));

    if (empty($email) || empty($password) || empty($role)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    try {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $dbRole = strtolower(trim($user['role']));

            if (!password_verify($password, $user['password'])) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid password.']);
                exit;
            }

            if ($role !== $dbRole) {
                echo json_encode(['status' => 'error', 'message' => 'Role mismatch.']);
                exit;
            }

            $_SESSION['user'] = [
                'user_id'  => $user['id'],
                'username' => $user['username'],
                'email'    => $user['email'],
                'role'     => $user['role'],
                'place'    => $user['place'] ?? null
            ];

            echo json_encode([
                'status'  => 'success',
                'message' => 'Login successful!',
                'data'    => ['role' => $user['role']]
            ]);
            exit;

        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found.']);
            exit;
        }

    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
exit;
?>

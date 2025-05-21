<?php
require_once 'db.php'; // Use your PDO connection

$message = "";

// Preserve the type from GET on first load, or POST on submission
$type = $_GET['type'] ?? $_POST['type'] ?? null; 
$back_link = $type === 'drink' ? 'drink/drink.html' : 'food/res.html';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST["name"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $subject = htmlspecialchars(trim($_POST["subject"]));
    $body = htmlspecialchars(trim($_POST["message"]));

    if (empty($name) || empty($email) || empty($subject) || empty($body)) {
        $message = "All fields are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $body]);
            $message = "Message sent successfully!";
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Us</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .contact-container {
      background: white;
      padding: 40px;
      box-shadow: 0 0 15px rgba(0,0,0,0.2);
      border-radius: 8px;
      width: 100%;
      max-width: 500px;
    }

    .contact-form h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .contact-form input,
    .contact-form textarea {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .contact-form button {
      width: 100%;
      padding: 12px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }

    .contact-form button:hover {
      background-color: #0056b3;
    }

    .message {
      text-align: center;
      margin-bottom: 15px;
      color: green;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="contact-container">
    <form method="POST" class="contact-form">
  <h2>Contact Us</h2>
  <?php if (!empty($message)): ?>
    <div class="message"><?php echo htmlspecialchars($message); ?></div>
  <?php endif; ?>
  <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>" />
  <input type="text" name="name" placeholder="Your Name" required />
  <input type="email" name="email" placeholder="Your Email" required />
  <input type="text" name="subject" placeholder="Subject" required />
  <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
  <button type="submit">Send Message</button>
</form>
<a href="<?php echo $back_link; ?>">🔙 Back</a>


  </div>
</body>
</html>

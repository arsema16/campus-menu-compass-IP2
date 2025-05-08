<?php
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    
    // For now, just echo the data as a test (You can replace this with email or DB logic)
    echo "<h2>Thank you for contacting us!</h2>";
    echo "<p><strong>Name:</strong> $name</p>";
    echo "<p><strong>Email:</strong> $email</p>";
    echo "<p><strong>Message:</strong> $message</p>";
    
    // You can add your email sending or database storing logic here if needed
}

echo '
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600&display=swap" rel="stylesheet" />
    <style>
      body {
        font-family: "Poppins", sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
      }
      header {
        background-color: #333;
        color: white;
        padding: 10px 0;
        text-align: center;
      }
      header nav a {
        color: white;
        margin: 0 15px;
        text-decoration: none;
        font-weight: bold;
      }
      .contact-container {
        width: 50%;
        margin: 30px auto;
        padding: 20px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      }
      .form-group {
        margin-bottom: 15px;
      }
      .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
      }
      .form-group input,
      .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
      }
      .form-group textarea {
        resize: vertical;
      }
      .submit-btn {
        background-color: #333;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
      }
      .submit-btn:hover {
        background-color: #555;
      }
      .footer {
        background-color: #333;
        color: white;
        padding: 30px;
        text-align: center;
      }
      .footer a {
        color: white;
        text-decoration: none;
        margin: 0 10px;
      }
    </style>
  </head>
  <body>
    <!-- Navigation Bar -->
    <header>
      <div class="logo">
        <img src="img/logo.jpg" alt="Logo" />
      </div>
      <nav>
        <a href="res.php">Home</a>
        <a href="contact.php">Contact</a>
        <a href="option.php">Back to Option</a>
      </nav>
    </header>

    <!-- Contact Form -->
    <div class="contact-container">
      <h1>Contact Us</h1>
      <form action="contact.php" method="POST">
        <div class="form-group">
          <label for="name">Full Name</label>
          <input type="text" id="name" name="name" placeholder="Your Full Name" required />
        </div>

        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" placeholder="Your Email Address" required />
        </div>

        <div class="form-group">
          <label for="message">Message</label>
          <textarea id="message" name="message" rows="4" placeholder="Your Message" required></textarea>
        </div>

        <button type="submit" class="submit-btn">Submit</button>
      </form>
    </div>

    <!-- Footer -->
    <section class="footer">
      <p>&copy; 2025</p>
    </section>
  </body>
</html>';
?>

<?php
session_start();
require_once 'db.php';

// Fetch user reviews from database
$reviews = $pdo->query("SELECT * FROM user_reviews ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Menu Choice</title>
  <link rel="stylesheet" href="option.css" />
</head>
<body>
  <div class="choice-container">
    <h1>What would you like to explore?</h1>
    <div class="choices">
      <div class="choice-card">
        <img src="op.png" alt="Food Menu" />
        <h2>Food Menu</h2>
        <a href="food/res.html" class="button">View Food Menu</a>
      </div>
      <div class="choice-card">
        <img src="dr.png" alt="Drink Menu" />
        <h2>Drink Menu</h2>
        <a href="drink/drink.html" class="button">View Drink Menu</a>
      </div>
    </div>
  </div>

  <!-- Review Submission Form -->
<?php if (isset($_SESSION['user'])): ?>
  <div class="review-form">
    <h2>Leave a Review</h2>
    <form action="submit_review.php" method="POST">
      <textarea name="review" rows="4" placeholder="Write your review here..." required></textarea>
      <button type="submit">Submit Review</button>
    </form>
  </div>
<?php else: ?>
  <p style="text-align:center; margin-top:40px; color:#555;">
    Please <a href="login1.html" style="color:#3498db; text-decoration:none;">log in</a> to leave a review.
  </p>
<?php endif; ?>

<!-- Review Display Section -->
<div class="review-section">
  <h2>User Reviews</h2>
  <div class="review-grid">
    <?php if (count($reviews) > 0): ?>
      <?php foreach ($reviews as $rev): ?>
        <div class="review-card">
          <div class="review-header">
            <div class="avatar"><?php echo strtoupper(substr($rev['username'], 0, 1)); ?></div>
            <div class="review-user-info">
              <strong><?php echo htmlspecialchars($rev['username']); ?></strong>
              <small><?php echo date("F j, Y", strtotime($rev['created_at'])); ?></small>
            </div>
          </div>
          <p class="review-text">"<?php echo htmlspecialchars($rev['review']); ?>"</p>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p style="text-align:center; color:#777;">No reviews yet. Be the first!</p>
    <?php endif; ?>
  </div>
</div>

</body>
</html>

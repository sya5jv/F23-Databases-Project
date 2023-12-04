<!-- JS Event Listener Tutorial: https://www.w3schools.com/js/js_htmldom_eventlistener.asp -->
<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

include 'connect-db.php';

// Get the movie name and release date
$name = $_GET['name'] ?? '';
$release_date = $_GET['release_date'] ?? '';

// SQL Query to get movie details
$sql = "SELECT * FROM Movie WHERE name = ? AND release_date = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$name, $release_date]);

$movie = $stmt->fetch(PDO::FETCH_ASSOC);

// SQL Query to get reviews 
$review_sql = "SELECT Review.*, Users.UserId, Users.username 
               FROM Review
               INNER JOIN Writes_Review ON Review.reviewId = Writes_Review.reviewID
               INNER JOIN Users ON Users.UserId = Writes_Review.userID
               INNER JOIN Has ON Review.reviewId = Has.reviewId
               WHERE Has.name = ? AND Has.release_date = ?";
$review_stmt = $db->prepare($review_sql);
$review_stmt->execute([$name, $release_date]);

$reviews = $review_stmt->fetchAll(PDO::FETCH_ASSOC);


if (!$movie) {
    echo "Movie not found.";
    exit;
}

include "navbar.php";

?>
<!DOCTYPE html>
<html>
    <style>
		input[type="number"] {
			width: 150px;
		}
	</style>
   <head>
      <meta charset="utf-8">
      <title><?= htmlspecialchars($movie['name']) ?></title>
      <link href="style.css" rel="stylesheet" type="text/css">
   </head>
   <body class="loggedin">
      <div class="content">
      <div class="movie-details">
         <div class="movie-title">
            <h2 class="movie-title">
            <?= htmlspecialchars($movie['name']) ?>
            <!-- Favorite -->
            <?php
               if (isset($_SESSION['loggedin'])) {
                   $userId = $_SESSION['UserId'];
                   $movieName = $movie['name'];
                   $movieReleaseDate = $movie['release_date'];
               
                   $checkFavoriteSql = "SELECT COUNT(*) FROM Watches WHERE UserId = ? AND name = ? AND release_date = ? AND favorite = 1";
                   $checkFavoriteStmt = $db->prepare($checkFavoriteSql);
                   $checkFavoriteStmt->execute([$userId, $movieName, $movieReleaseDate]);
                   $isFavorite = (bool) $checkFavoriteStmt->fetchColumn();
               
                   echo '<button style="float: right;" class="favorite-button" data-action="' . ($isFavorite ? 'unfavorite' : 'favorite') . '">' . ($isFavorite ? 'Unfavorite' : 'Favorite') . '</button>';
               }
               ?>
         </div>
         <p><strong>Release Date:</strong> <?= htmlspecialchars($movie['release_date']) ?></p>
         <p><strong>Runtime:</strong> <?= htmlspecialchars($movie['runtime']) ?> minutes</p>
         <p><strong>Genre:</strong> <?= htmlspecialchars($movie['genre']) ?></p>
         <p><strong>Summary:</strong> <?= nl2br(htmlspecialchars($movie['summary'])) ?></p>
         <p><strong>Average Score:</strong> <?= htmlspecialchars($movie['average_movie_score']) ?></p>
         <p><strong>Director:</strong> <?= htmlspecialchars($movie['director']) ?></p>
      </div>
      <?php if (isset($_SESSION['loggedin'])): ?>
      <div class="add-review">
         <h3>Add Your Review</h3>
         <form action="add-review.php" method="post">
            <input type="hidden" name="name" value="<?= htmlspecialchars($movie['name']) ?>">
            <input type="hidden" name="release_date" value="<?= htmlspecialchars($movie['release_date']) ?>">
            <textarea name="review_text" placeholder="Your review" required></textarea>
            <input type="number" name="rating" min="1" max="10" placeholder="Your rating (1-10)" required>
            <input type="submit" value="Submit Review">
         </form>
      </div>
      <?php endif; ?>
      <div class="reviews">
         <h3>Reviews</h3>
         <?php foreach ($reviews as $review): ?>
         <div class="review">
            <p><strong><?= htmlspecialchars($review['username']) ?></strong>:</p>
            <p><?= htmlspecialchars($review['review_text']) ?></p>
            <p><strong>Rating:</strong> <?= htmlspecialchars($review['rating']) ?> </p>
            <p> <strong>Date:</strong> <?= htmlspecialchars($review['review_time']) ?></p>
            <!-- Like Button -->
            <?php
               $checkLikeSql = "SELECT COUNT(*) FROM Likes WHERE UserId = ? AND reviewId = ?";
               $checkLikeStmt = $db->prepare($checkLikeSql);
               $checkLikeStmt->execute([$userId, $review['reviewId']]);
               $hasLiked = $checkLikeStmt->fetchColumn() > 0;
               
               $likeCountSql = "SELECT COUNT(*) FROM Likes WHERE reviewId = ?";
               $likeCountStmt = $db->prepare($likeCountSql);
               $likeCountStmt->execute([$review['reviewId']]);
               $likeCount = $likeCountStmt->fetchColumn();
               
               echo "<strong>Number of likes: </strong><span class='like-count' id='like-count-{$review['reviewId']}'>$likeCount</span>";
               echo "<br>";
               echo '<button class="like-button" data-review-id="' . $review['reviewId'] . '" data-action="' . ($hasLiked ? 'unlike' : 'like') . '">' . ($hasLiked ? 'Unlike' : 'Like') . '</button>';
               ?>
            <p>----------------------------------</p>
            <!-- Replies -->
            <?php
               $replies_sql = "SELECT Replies.*, Users.username 
                               FROM Replies
                               INNER JOIN Users ON Users.UserId = Replies.userId
                               WHERE Replies.reviewId = ?";
               $replies_stmt = $db->prepare($replies_sql);
               $replies_stmt->execute([$review['reviewId']]);
               $replies = $replies_stmt->fetchAll(PDO::FETCH_ASSOC);
               
               if (!empty($replies)): ?>
            <p><em>Replies:</em></p>
            <?php foreach ($replies as $reply): ?>
            <div class="reply" style="margin-left: 20px;">
               <p><strong><?= htmlspecialchars($reply['username']) ?></strong>: <?= htmlspecialchars($reply['reply_content']) ?></p>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
            <!-- Reply form -->
            <button class="reply-button">Reply</button>
            <form class="reply-form" style="display: none;" action="add-reply.php" method="post">
               <input type="hidden" name="reviewId" value="<?= $review['reviewId'] ?>">
               <input type="hidden" name="movieName" value="<?= htmlspecialchars($movie['name']) ?>">
               <input type="hidden" name="movieReleaseDate" value="<?= htmlspecialchars($movie['release_date']) ?>">
               <textarea name="reply_text" placeholder="Your reply" required></textarea>
               <button type="submit">Submit</button>
               <button type="button" class="cancel-button">Cancel</button>
            </form>
            <?php if ($_SESSION['UserId'] == $review['UserId']): ?>
            <!-- Delete form -->
            <form action="delete-review.php" method="post" style="display: inline;">
               <input type="hidden" name="reviewId" value="<?= $review['reviewId'] ?>">
               <input type="hidden" name="movieName" value="<?= htmlspecialchars($movie['name']) ?>">
               <input type="hidden" name="movieReleaseDate" value="<?= htmlspecialchars($movie['release_date']) ?>">
               <input type="submit" value="Delete" style="float: right;">
            </form>
            <?php endif; ?>
         </div>
         <?php endforeach; ?>
      </div>
      <script>
         document.querySelectorAll('.like-button').forEach(button => {
         button.addEventListener('click', function() {
         const reviewId = this.getAttribute('data-review-id');
         const action = this.getAttribute('data-action');
         const likeCountElement = document.getElementById(`like-count-${reviewId}`);
         
         fetch(action === 'like' ? 'like.php' : 'unlike.php', {
             method: 'POST',
             body: JSON.stringify({ reviewId }),
             headers: { 'Content-Type': 'application/json' }
         })
         .then(response => response.json())
         .then(data => {
             if (data.success) {
                 this.textContent = action === 'like' ? 'Unlike' : 'Like';
                 this.setAttribute('data-action', action === 'like' ? 'unlike' : 'like');
         
                 // Update the like count
                 let likeCount = parseInt(likeCountElement.textContent);
                 likeCount = action === 'like' ? likeCount + 1 : likeCount - 1;
                 likeCountElement.textContent = likeCount;
             } else {
                 alert(data.message);
             }
         });
         });
         });
         
         
          // JavaScript to toggle visibility of reply form when clicking "Reply"
          const replyButtons = document.querySelectorAll('.reply-button');
          const replyForms = document.querySelectorAll('.reply-form');
          const cancelButtons = document.querySelectorAll('.cancel-button');
          
          replyButtons.forEach((button, index) => {
              button.addEventListener('click', () => {
                  // Hide other reply forms and hide the clicked "Reply" button
                  replyForms.forEach((form, formIndex) => {
                      if (formIndex !== index) {
                          form.style.display = 'none';
                          replyButtons[formIndex].style.display = 'block';
                      }
                  });
                  
                  // Toggle visibility of the clicked reply form and hide the "Reply" button
                  replyForms[index].style.display = replyForms[index].style.display === 'block' ? 'none' : 'block';
                  button.style.display = 'none';
              });
              
              cancelButtons[index].addEventListener('click', () => {
                  // Hide the clicked reply form and show the "Reply" button
                  replyForms[index].style.display = 'none';
                  button.style.display = 'block';
              });
          });
          // JavaScript to handle favorite/unfavorite functionality
          const favoriteButton = document.querySelector('.favorite-button');
          
          if (favoriteButton) {
              favoriteButton.addEventListener('click', () => {
                  const action = favoriteButton.getAttribute('data-action');
          
                  if (action === 'favorite') {
                      // Add to favorites
                      fetch('favorite-movie.php', {
                          method: 'POST',
                          body: JSON.stringify({
                              name: '<?= $movie['name'] ?>',
                              release_date: '<?= $movie['release_date'] ?>'
                          }),
                          headers: {
                              'Content-Type': 'application/json'
                          }
                      }).then(response => response.json()).then(data => {
                          if (data.success) {
                              favoriteButton.textContent = 'Unfavorite';
                              favoriteButton.setAttribute('data-action', 'unfavorite');
                          }
                      });
                  } else if (action === 'unfavorite') {
                      // Remove from favorites
                      fetch('unfavorite-movie.php', {
                          method: 'POST',
                          body: JSON.stringify({
                              name: '<?= $movie['name'] ?>',
                              release_date: '<?= $movie['release_date'] ?>'
                          }),
                          headers: {
                              'Content-Type': 'application/json'
                          }
                      }).then(response => response.json()).then(data => {
                          if (data.success) {
                              favoriteButton.textContent = 'Favorite';
                              favoriteButton.setAttribute('data-action', 'favorite');
                          }
                      });
                  }
              });
          }
      </script>
   </body>
</html>

<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

include 'connect-db.php';

$userId = $_SESSION['UserId'];

$reviewId = $_POST['reviewId'] ?? '';
$movieName = $_POST['movieName'] ?? '';
$movieReleaseDate = $_POST['movieReleaseDate'] ?? '';

$verify_sql = "SELECT Writes_Review.UserId FROM Review
               INNER JOIN Writes_Review ON Review.reviewId = Writes_Review.reviewID
               WHERE Review.reviewId = ?";
$verify_stmt = $db->prepare($verify_sql);
$verify_stmt->execute([$reviewId]);
$commentOwner = $verify_stmt->fetch(PDO::FETCH_ASSOC)['UserId'];

if ($commentOwner == $userId) {

    $delete_has_sql = "DELETE FROM Has WHERE reviewId = ?";
    $delete_has_stmt = $db->prepare($delete_has_sql);
    $delete_has_stmt->execute([$reviewId]);

    $delete_writes_review_sql = "DELETE FROM Writes_Review WHERE reviewId = ?";
    $delete_writes_review_stmt = $db->prepare($delete_writes_review_sql);
    $delete_writes_review_stmt->execute([$reviewId]);

    $delete_replies_sql = "DELETE FROM Replies WHERE reviewId = ?";
    $delete_replies_stmt = $db->prepare($delete_replies_sql);
    $delete_replies_stmt->execute([$reviewId]);

    $delete_review_sql = "DELETE FROM Review WHERE reviewId = ?";
    $delete_review_stmt = $db->prepare($delete_review_sql);
    $delete_review_stmt->execute([$reviewId]);

    $recalculate_avg_sql = "UPDATE Movie SET average_movie_score = (
        SELECT AVG(rating) FROM Review WHERE reviewId IN (
            SELECT reviewId FROM Has WHERE name = ? AND release_date = ?
        )
    ) WHERE name = ? AND release_date = ?";
    $recalculate_avg_stmt = $db->prepare($recalculate_avg_sql);
    $recalculate_avg_stmt->execute([$movieName, $movieReleaseDate, $movieName, $movieReleaseDate]);

    header('Location: movie.php?name=' . urlencode($movieName) . '&release_date=' . urlencode($movieReleaseDate));
    exit;
} else {
    echo "You do not have permission to delete this comment.";
    exit;
}
?>

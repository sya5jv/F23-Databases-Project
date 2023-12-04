<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

$userId = $_SESSION['UserId'];

include 'connect-db.php';

$name = $_POST['name'] ?? '';
$release_date = $_POST['release_date'] ?? '';
$review_text = $_POST['review_text'] ?? '';
$rating = $_POST['rating'] ?? '';

$insert_sql = "INSERT INTO Review (rating, review_text, review_time) VALUES (?, ?, CURRENT_TIMESTAMP)";
$insert_stmt = $db->prepare($insert_sql);
$insert_stmt->execute([$rating, $review_text]);

$reviewId = $db->lastInsertId(); 

$has_sql = "INSERT INTO Has (reviewId, name, release_date) VALUES (?, ?, ?)";
$has_stmt = $db->prepare($has_sql);
$has_stmt->execute([$reviewId, $name, $release_date]);

$writes_sql = "INSERT INTO Writes_Review (reviewID, userID) VALUES (?, ?)";
$writes_stmt = $db->prepare($writes_sql);
$writes_stmt->execute([$reviewId, $userId]);

$calculate_avg_sql = "SELECT AVG(rating) AS new_average_score
                      FROM Review
                      NATURAL JOIN Has 
                      WHERE Has.name = ? AND Has.release_date = ?";
$calculate_avg_stmt = $db->prepare($calculate_avg_sql);
$calculate_avg_stmt->execute([$name, $release_date]);
$newAverageScore = $calculate_avg_stmt->fetch(PDO::FETCH_ASSOC)['new_average_score'];

$update_avg_sql = "UPDATE Movie SET average_movie_score = ? WHERE name = ? AND release_date = ?";
$update_avg_stmt = $db->prepare($update_avg_sql);
$update_avg_stmt->execute([$newAverageScore, $name, $release_date]);



header('Location: movie.php?name=' . urlencode($name) . '&release_date=' . urlencode($release_date));
exit;
?>

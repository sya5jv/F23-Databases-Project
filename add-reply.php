<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

include 'connect-db.php';

$reviewId = $_POST['reviewId'] ?? '';
$movieName = $_POST['movieName'] ?? '';
$movieReleaseDate = $_POST['movieReleaseDate'] ?? '';
$replyText = $_POST['reply_text'] ?? '';
$userId = $_SESSION['UserId'];

if (empty($reviewId) || empty($replyText)) {
    header("Location: movie.php?name=" . urlencode($movieName) . "&release_date=" . urlencode($movieReleaseDate) . "&error=MissingFields");
    exit;
}

$insertSql = "INSERT INTO Replies (userId, reviewId, reply_content) VALUES (?, ?, ?)";
$insertStmt = $db->prepare($insertSql);

try {
    $insertStmt->execute([$userId, $reviewId, $replyText]);
} catch (PDOException $e) {
    header("Location: movie.php?name=" . urlencode($movieName) . "&release_date=" . urlencode($movieReleaseDate) . "&error=DatabaseError");
    exit;
}

header("Location: movie.php?name=" . urlencode($movieName) . "&release_date=" . urlencode($movieReleaseDate));
exit;
?>

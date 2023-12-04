<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    echo json_encode(['success' => false, 'message' => 'User is not logged in']);
    exit;
}

include 'connect-db.php';

$userId = $_SESSION['UserId'];
$reviewId = $_POST['reviewId'];

$checkLikeSql = "SELECT COUNT(*) FROM Likes WHERE UserId = ? AND reviewId = ?";
$checkLikeStmt = $db->prepare($checkLikeSql);
$checkLikeStmt->execute([$userId, $reviewId]);

if ($checkLikeStmt->fetchColumn() > 0) {
    echo json_encode(['success' => false, 'message' => 'User has already liked this comment']);
    exit;
}

$insertLikeSql = "INSERT INTO Likes (UserId, reviewId) VALUES (?, ?)";
$insertLikeStmt = $db->prepare($insertLikeSql);

if ($insertLikeStmt->execute([$userId, $reviewId])) {
    echo json_encode(['success' => true, 'message' => 'Comment liked successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to like comment']);
}
?>

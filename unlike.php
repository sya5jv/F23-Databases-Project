<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    echo json_encode(['success' => false, 'message' => 'User is not logged in']);
    exit;
}

include 'connect-db.php';
$data = json_decode(file_get_contents('php://input'), true);
$userId = $_SESSION['UserId'];
$reviewId = $data['reviewId'];

$checkLikeSql = "SELECT COUNT(*) FROM Likes WHERE UserId = ? AND reviewId = ?";
$checkLikeStmt = $db->prepare($checkLikeSql);
$checkLikeStmt->execute([$userId, $reviewId]);

if ($checkLikeStmt->fetchColumn() === 0) {
    echo json_encode(['success' => false, 'message' => 'User has not liked this review']);
    exit;
}

$deleteLikeSql = "DELETE FROM Likes WHERE UserId = ? AND reviewId = ?";
$deleteLikeStmt = $db->prepare($deleteLikeSql);
$deleteLikeStmt->execute([$userId, $reviewId]);

$newLikeCountSql = "SELECT COUNT(*) FROM Likes WHERE reviewId = ?";
$newLikeCountStmt = $db->prepare($newLikeCountSql);
$newLikeCountStmt->execute([$reviewId]);
$newLikeCount = $newLikeCountStmt->fetchColumn();

echo json_encode(['success' => true, 'newLikeCount' => $newLikeCount, 'message' => 'Review unliked successfully']);
?>

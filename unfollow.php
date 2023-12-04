<?php
session_start();
include 'connect-db.php';

$data = json_decode(file_get_contents('php://input'), true);
$userId = $_SESSION['UserId'];
$profileUserId = $data['userId'];

$deleteSql = "DELETE FROM Follows WHERE user1_userId = ? AND user2_userId = ?";
$deleteStmt = $db->prepare($deleteSql);
if ($deleteStmt->execute([$userId, $profileUserId])) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to unfollow']);
}
?>

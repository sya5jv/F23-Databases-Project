<?php
session_start();
include 'connect-db.php';

$data = json_decode(file_get_contents('php://input'), true);
$userId = $_SESSION['UserId'];
$profileUserId = $data['userId'];

$insertSql = "INSERT INTO Follows (user1_userId, user2_userId) VALUES (?, ?)";
$insertStmt = $db->prepare($insertSql);
if ($insertStmt->execute([$userId, $profileUserId])) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to follow']);
}
?>

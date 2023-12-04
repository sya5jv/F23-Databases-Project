<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

$userId = $_SESSION['UserId'];

include 'connect-db.php';

$commenter_userId = $_POST['commenter_userId'] ?? '';
$commentee_userId = $_POST['commentee_userId'] ?? '';
$comment_content = $_POST['comment_content'] ?? '';
$username = $_POST['username'] ?? '';
$score = $_POST['score'] ?? '';

$insert_sql = "INSERT INTO Comment (commenter_userId, commentee_userId, score, comment_content) VALUES (?, ?, ?, ?)";
$insert_stmt = $db->prepare($insert_sql);
$insert_stmt->execute([$commenter_userId, $commentee_userId, $score, $comment_content]);

$CommentId = $db->lastInsertId(); 

$writes_sql = "INSERT INTO Writes_Comment (CommentID, userID) VALUES (?, ?)";
$writes_stmt = $db->prepare($writes_sql);
$writes_stmt->execute([$CommentId, $commenter_userId]);

header('Location: profile.php?username=' . $username);
exit;
?>

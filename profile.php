<?php

session_start();
require 'connect-db.php';
// We need to use sessions, so you should always start sessions using the below code.
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}

// We don't have the password or email info stored in sessions, so instead, we can get the results from the database.
// In this case we can use the account ID to get the account info.

// Keep track of whether or not the current profile query belongs to the current user
$is_current_user_profile = false;

if(!isset($_GET['username']) || $_GET['username'] == $_SESSION['Users']) {
	// Current user's profile
	$username = $_SESSION['Users'];
	$stmt = $db->prepare('SELECT password, UserId, age, bio FROM Users WHERE :username = username');
	$GLOBALS['is_current_user_profile'] = true;
} else {
	// Other user's profile
	$username = $_GET['username'];
	$stmt = $db->prepare('SELECT UserId, age, bio FROM Users WHERE :username = username'); // Note: No password query needed
}

$stmt->bindValue('username', $username);
$stmt->execute();
$results = $stmt->fetch();

// parameters for the Users account

if ($is_current_user_profile) {
	$pass = $results['password'];
}
$bio = $results['bio'];
$age = $results['age'];
$stmt->closeCursor();


$isFollowing = false;
if (!$is_current_user_profile) {
    $followCheckSql = "SELECT COUNT(*) FROM Follows WHERE user1_userId = ? AND user2_userId = ?";
    $followCheckStmt = $db->prepare($followCheckSql);
    $followCheckStmt->execute([$_SESSION['UserId'], $results['UserId']]);
    $isFollowing = $followCheckStmt->fetchColumn() > 0;
}

$follow_count_sql = "SELECT COUNT(*) 
					FROM Follows
					WHERE user2_userId = {$results['UserId']}";
$follow_count_stmt = $db->prepare($follow_count_sql);
$follow_count_stmt->execute();
$follow_count = $follow_count_stmt->fetch();

// $following_sql = "SELECT *
// 				FROM Follows
// 				JOIN Users ON Follows.user2_userId = Users.UserId
// 				WHERE Follows.user1_userId = {$results['UserId']}";
// $following_sql_stmt = $db->prepare($following_sql);
// $following_sql_stmt->execute();
// $following = $following_sql_stmt->fetch();
// print_r($following);

$comment_sql = "SELECT Comment.*, Users.UserId, Users.username
				FROM Comment, Users
				WHERE Comment.commentee_userId = {$results['UserId']} AND Comment.commenter_userId = Users.UserId";
$comment_stmt = $db->prepare($comment_sql);
$comment_stmt->execute();

$comments = $comment_stmt->fetchAll(PDO::FETCH_ASSOC);

include 'navbar.php';
?>



<!DOCTYPE html>
<html>
	<style>
		input[type="number"] {
			width: 300px;
		}
	</style>
	<head>
		<meta charset="utf-8">
		<title>Profile Page</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
	</head>
	<body class="loggedin">
		<div class="content">
			<h2>Profile Page</h2>
			<?php if($is_current_user_profile): ?>
			<div>
				<p>Your account details are below:</p>
				<table>
				<tr>
					<td>Username:</td>
					<td><?=$_SESSION['Users']?></td>
				</tr>
				<tr>
					<td>Password:</td>
					<td><?php echo "********"?></td>
				</tr>
				<tr>
					<td>Bio:</td>
					<td><?=$bio?></td>
				</tr>
				<tr>
					<td>Age:</td>
					<td><?=$age?></td>
				</tr>
				<tr>
					<td>Followers:</td>
					<!-- #TODO: Fix this -->
					<td><?=$follow_count[0]?></td>
				</tr>
				</table>
				<button onclick="window.location.href='updateProfile.php'">Update Profile</button>
			</div>
			<?php else: ?>
			<div>
				<p><?=$username?>'s Profile:
				<p>
				<table>
				<tr>
					<td>Username:</td>
					<td><?=$username?></td>
				</tr>
				<tr>
					<td>Bio:</td>
					<td><?=$bio?></td>
				</tr>
				<tr>
					<td>Age:</td>
					<td><?=$age?></td>
				</tr>
				<tr>
					<td>Followers: <?=$follow_count[0]?></td>
				</tr>
				</table>
				<button class="follow-button" data-action="<?= $isFollowing ? 'unfollow' : 'follow' ?>" data-user-id="<?= $results['UserId']?>">
				<?= $isFollowing ? 'Unfollow' : 'Follow' ?>
				</button>
			</div>
			<?php endif; ?>
			<!-- <h3>Following:</h3>
			<?php foreach ($following as $follow): ?>
				<p><?=$follow?></p>	
			<?php endforeach; ?> -->
			<h3>Comments</h3>
			<?php foreach ($comments as $comment): ?>
			<div class="comment">
						<!-- <p><?php print_r($comment); ?></p> -->
				<p><strong><?=$comment['username']?></strong></p>
				<p><?=$comment['comment_content']?></p>
				<p><i>Score: <?=$comment['score']?></i></p>
			</div>
			<?php endforeach; ?>
			<?php if (!$is_current_user_profile): ?>
				<div class="add-comment">
					<h3>Add Comment</h3>
					<form action="add-comment.php" method="post">
						<input type="hidden" name="commenter_userId" value="<?=$_SESSION['UserId']?>" />
						<input type="hidden" name="commentee_userId" value="<?=$comment['commentee_userId']?>" />
						<input type="hidden" name="commentee_username" value="<?=$username?>" />
						<textarea name="comment_content" placeholder="Your comment here" required></textarea>
						<input type="number" name="score" min="1" max="10" placeholder="Leave a score to rate this user's profile" required>
						<input type="submit" value="Post Comment">
					</form>
				</div>
			<?php endif; ?>
		</div>
		<script>
			document.querySelector('.follow-button').addEventListener('click', function() {
				var userId = this.getAttribute('data-user-id');
				var action = this.getAttribute('data-action');
				var username = this.getAttribute('data-username');
			
				fetch(action === 'follow' ? 'follow.php' : 'unfollow.php', {
					method: 'POST',
					body: JSON.stringify({ userId: userId }),
					headers: { 'Content-Type': 'application/json' }
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						this.textContent = action === 'follow' ? 'Unfollow' : 'Follow';
						this.setAttribute('data-action', action === 'follow' ? 'unfollow' : 'follow');
					} else {
						alert(data.message);
					}
				});
			});
		</script>
	</body>
</html>

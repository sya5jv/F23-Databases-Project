<?php
require 'connect-db.php';
// We need to use sessions, so you should always start sessions using the below code.
// If the user is not logged in redirect to the login page...

session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}

//  $db = new PDO($dsn, $username, $password);
$username = $_SESSION['Users'];
// We don't have the password or email info stored in sessions, so instead, we can get the results from the database.
$stmt = $db->prepare('SELECT password, age, bio FROM Users WHERE :username = username');
// In this case we can use the account ID to get the account info.

$stmt->bindValue('username', $username);
$stmt->execute();
$results = $stmt->fetch();

// parameters for the Users account
$pass = $results['password'];
$bio = $results['bio'];
$age = $results['age'];
$stmt->closeCursor();
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Profile Page</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>Fresh Tomatoes</h1>

				<a href="home.php"><i class="fas fa-user-circle"></i>Home</a>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content">
			<h2>Profile Page</h2>
			<div>
				<p>Your account details are below:</p>
				<table>
					<tr>
						<td>Username:</td>
						<td><?=$_SESSION['Users']?></td>
					</tr>
					<tr>
						<td>Password:</td>
						<td><?=$pass?></td>
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
						<td>Friend:</td>
						<!-- <td><?=$bio?></td> -->
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>
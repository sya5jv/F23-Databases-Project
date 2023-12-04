<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (isset($_SESSION['loggedin'])) {
	header('Location: home.php');
	exit;
}
?>

<!DOCTYPE html>
<link href="style.css" rel="stylesheet" type="text/css">

<html>
	<head>
		<meta charset="utf-8">
		<title>Login</title>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body>
		<div class="login">
			<h1>Login</h1>
			<form action="authenticate.php" method="post">
				<label for="username">
					<i class="fas fa-user"></i>
				</label>
				<input type="text" name="username" placeholder="Username" id="username" required>
				<label for="password">
					<i class="fas fa-lock"></i>
				</label>
				<input type="password" name="password" placeholder="Password" id="password" required>
				<input type="submit" value="Login">
			</form>

			<form action="register.html" method="post">
			<input type="submit" value="Register">

			</form>
		</div>
	</body>
</html>
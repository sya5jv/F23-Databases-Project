<?php

session_start();
require 'connect-db.php';
// We need to use sessions, so you should always start sessions using the below code.
// If the user is not logged in redirect to the login page...
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


if ($_SERVER["REQUEST_METHOD"] == 'POST')
{
    if(!empty($_POST['UpdateProfileBtn']))
    {
        $age = $_POST['age'];
        $bio = $_POST['bio'];
        $pass = $_POST['password'];

        $_SESSION['password'] = $pass;
        $pass = password_hash($pass, PASSWORD_BCRYPT, array("cost" => 12));

        $query = "update Users set username = :username, password = :password, bio = :bio, age = :age where username = :username";

        $stmt = $db->prepare($query);
        $stmt->bindValue('username', $username);
        $stmt->bindValue('bio', $bio);
        $stmt->bindValue('age', $age);
        $stmt->bindValue('password', $pass);
        $stmt->execute();
        $stmt->closeCursor();
        echo "<script>alert('Profile Updated'); window.location.href='profile.php'</script>";
    }
}
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

		<div class="container">
  <h2>Update Profile Page</h2>  

  <!-- <a href="simpleform.php">Click to open the next page</a> -->
  <form name="mainForm" action="updateProfile.php" method="post">   
	<p> Your account details are below: </p>
      <!-- <div class="row mb-3 mx-3">
        Username:
        <input type="text" class="form-control" name="username" required 
        value="<?php echo $username; ?>"   />
        </div> -->
      <div class="row mb-3 mx-3">
        Password:

         <input type="hidden" name="pass_to_update" value="<?php echo "******"; ?>"/>
        <input type="text" class="form-control" name="password" required value = "<?php echo $_SESSION['password'];?>"  
        value="<?php echo @$_POST['pass_to_update']; ?>"   />

        </div>
      <div class="row mb-3 mx-3">
        Age:
         <input type="hidden" name="age_to_update" value="<?php echo $age; ?>"/>
        <input type="text" class="form-control" name="age" required    value = "<?php echo $age;?>"  
        value="<?php echo @$_POST['age_to_update']; ?>"   />
      </div>  
      <div class="row mb-3 mx-3">
        Bio:

         <input type="hidden" name="bio_to_update" value="<?php echo $bio; ?>"/>
        <input type="text" class="form-control" name="bio" required value = "<?php echo $bio;?>"
        value="<?php echo @$_POST['bio_to_update']; ?>"   />

      </div>  
	  <div class="row mb-3 mx-3">
        <input type="submit" value="Update Profile" name="UpdateProfileBtn" 
                class="btn btn-secondary" title="Update your Profile" />
      </div>  

	</body>
</html>
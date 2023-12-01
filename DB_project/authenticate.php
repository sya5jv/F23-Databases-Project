
 <?php
require 'connect-db.php'; //require connection script

        // try {
            session_start();
            $db = new PDO($dsn, $username, $password);

    //ensure fields are not empty
    $username = !empty($_POST['username']) ? trim($_POST['username']) : null;
    $passwordAttempt = !empty($_POST['password']) ? trim($_POST['password']) : null;
    
    //Retrieve the user account information for the given username.
    $sql = "SELECT UserId, username, password FROM Users WHERE username = :username";
    $stmt = $db->prepare($sql);
    
    //Bind value.
    $stmt->bindValue(':username', $username);
    
    //Execute.
    $stmt->execute();
    
    //Fetch row.
    $user = $stmt->fetch();
            
    // print($stmt->fetch(PDO::FETCH_ASSOC));

    // echo $stmt->fetch(PDO::FETCH_ASSOC);
    //If $row is FALSE.
    if($user === false){
       echo '<script>alert("invalid username or password")</script>';
    }

    else{

        // NOTE: Use when we have hashed passwords
        $validPassword = password_verify($passwordAttempt, $user['password']);
        if($validPassword){

        // if($passwordAttempt == $user['password']){
            
            //Provide the user with a login session.
             
            $_SESSION['Users'] = $_POST['username'];
            $_SESSION['loggedin'] = TRUE;
            header('Location: home.php');
            exit;
            
        } 
        else{
            echo '<script>alert("invalid username or password")</script>';
        }
    }
?>


<?php

    require 'connect-db.php'; //require connection script

    session_start();
    $db = new PDO($dsn, $username, $password);


  
         $user = $_POST['username'];
         $pass = $_POST['password'];
         $bio = $_POST['bio'];
         $age = $_POST['age'];
         
         //encrypt password
         $pass = password_hash($pass, PASSWORD_BCRYPT, array("cost" => 12));
          
         //Check if username exists
         $sql = "SELECT COUNT(username) AS num FROM Users WHERE username = :username";
         $stmt = $db->prepare($sql);

         $stmt->bindValue(':username', $user);
         $stmt->execute();
         $row = $stmt->fetch(PDO::FETCH_ASSOC);


         // check if username already exist, guess it makes it easier for us
         if($row['num'] > 0){
             echo "<script>alert('Username already exists'); window.location.href='register.html'</script>";

        }
        
        else{

            $stmt = $db->prepare("INSERT INTO Users (bio, age, username, password) VALUES (:bio, :age, :username, :password)");
            $stmt->bindParam(':bio', $bio);
            $stmt->bindParam(':age', $age);
            $stmt->bindParam(':username', $user);
            $stmt->bindParam(':password', $pass);
            $stmt->execute();
            echo "<script>alert('New account created.'); window.location.href='index.html'</script>";

        }
    

?>
 
 
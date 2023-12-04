<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

include 'connect-db.php';

$search_query = $_GET['search_query'] ?? '';


$sql = "SELECT * FROM Users WHERE username LIKE ?";
$stmt = $db->prepare($sql); 
$stmt->execute(["%$search_query%"]);

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Search Results</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body class="loggedin search-page"> 
    <nav class="navtop">
        <div>
            <h1>Fresh Tomatoes</h1>
            <a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
            <form action="search.php" method="get" class="searchbar">
                <input type="text" name="search_query" placeholder="Search for movies..." class="search-input">
                <input type="submit" value="Search" class="search-button">
            </form>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
        </div>
    </nav>
    <div class="content">
        <h2>Search for Users</h2>
        <form action="people-search.php" method="get" class="searchbar">
            <input type="text" name="search_query" placeholder="Search for people..." class="search-input">
            <input type="submit" value="Search" class="search-button">
        </form>
        <h2>Search Results</h2>
        <?php foreach ($users as $user): ?>
            <div class="user">
                <a href="profile.php?username=<?= urlencode($user['username'])?>" class="profile-link">
                    <div class="user-details">
                        <h3><?=$user['username'] ?></h3>
                        <p>Biography: <?=$user['bio'] ?></p>
                        <!-- <p>Followers: <?=$user['followers'] ?></p> -->
                    </div>
                </a>
                <input type="button" value="Follow" class="follow-btn">
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

include 'connect-db.php';
include 'navbar.php';

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
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>

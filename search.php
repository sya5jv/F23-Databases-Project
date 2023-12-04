<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

include 'connect-db.php';

$search_query = $_GET['search_query'] ?? '';


$sql = "SELECT * FROM Movie WHERE name LIKE ? OR genre LIKE ?";
$stmt = $db->prepare($sql); 
$stmt->execute(["%$search_query%", "%$search_query%"]);

$movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "navbar.php";

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
        <h2>Search Results</h2>
        <?php foreach ($movies as $movie): ?>
            <div class="movie">
                <a href="movie.php?name=<?= urlencode($movie['name']) ?>&release_date=<?= urlencode($movie['release_date']) ?>" class="movie-link">
                    <div class="movie-details">
                        <h3><?= htmlspecialchars($movie['name']) ?></h3>
                        <p><?= htmlspecialchars($movie['summary']) ?></p>
                    </div>
                    <div class="movie-score">
                        <div class="rating-circle">
                            <?= htmlspecialchars(number_format($movie['average_movie_score'], 1)) ?>
                        </div>
                        <p>Average Rating</p>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>

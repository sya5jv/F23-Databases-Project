<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestData = json_decode(file_get_contents('php://input'), true);

    if ($requestData === null) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit;
    }

    include 'connect-db.php';

    $userId = $_SESSION['UserId'];
    $movieName = $requestData['name'];
    $releaseDate = $requestData['release_date'];

    $checkSql = "SELECT COUNT(*) FROM Watches WHERE UserId = ? AND name = ? AND release_date = ?";
    $checkStmt = $db->prepare($checkSql);
    $checkStmt->execute([$userId, $movieName, $releaseDate]);

    if ($checkStmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Movie is already in favorites']);
        exit;
    }

    $insertSql = "INSERT INTO Watches (UserId, name, release_date, favorite) VALUES (?, ?, ?, 1)";
    $insertStmt = $db->prepare($insertSql);

    if ($insertStmt->execute([$userId, $movieName, $releaseDate])) {
        echo json_encode(['success' => true, 'message' => 'Movie added to favorites']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add movie to favorites']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>

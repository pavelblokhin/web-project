<?php
    session_start();
    require 'db.php';

    $conn = new PDO("mysql:host=localhost;dbname=game_db", "root", "");


    $game_id = $_POST['game_id'];

    function generateRandomCells($numCells, $gridSize) {
        $cells = [];
        for ($i = 0; $i < $numCells; $i++) {
            $x = rand(1, $gridSize);
            $y = rand(1, $gridSize);
            $buyers = rand(1, 5);
            $cells[] = ['x' => $x, 'y' => $y, 'buyers' => $buyers];
        }
        return $cells;
    }

    function deleteOldCells($game_id) {
        global $conn;
        // $conn = new PDO("mysql:host=localhost;dbname=game_db", "root", "");
        $stmt = $conn->prepare("DELETE FROM game_cells WHERE game_id = :game_id");
        $stmt->bindParam(':game_id', $game_id);
        $stmt->execute();
    }

    function saveRandomCells($game_id, $cells) {
        global $conn;
        // $conn = new PDO("mysql:host=localhost;dbname=game_db", "root", "");
        foreach ($cells as $cell) {
            $stmt = $conn->prepare("INSERT INTO game_cells (game_id, x, y, buyers) VALUES (:game_id, :x, :y, :buyers) ON DUPLICATE KEY UPDATE buyers = :buyers");
            $stmt->bindParam(':game_id', $game_id);
            $stmt->bindParam(':x', $cell['x']);
            $stmt->bindParam(':y', $cell['y']);
            $stmt->bindParam(':buyers', $cell['buyers']);
            $stmt->execute();
        }
    }

    if ($_POST['action'] == 'generate') {
        deleteOldCells($game_id);
        $cells = generateRandomCells(20, 10);
        saveRandomCells($game_id, $cells);
        $_SESSION['game_cells'] = $cells;
        echo json_encode(['status' => 'success', 'cells' => $cells]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
?>

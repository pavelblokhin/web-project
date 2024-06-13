<?php
    session_start();

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    try {
        $conn = new PDO("mysql:host=localhost;dbname=game_db", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $game_id = $_GET['game_id'];

        function getRandomCells($game_id) {
            $conn = new PDO("mysql:host=localhost;dbname=game_db", "root", "");
            $stmt = $conn->prepare("SELECT x, y, buyers FROM game_cells WHERE game_id = :game_id");
            $stmt->bindParam(':game_id', $game_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $cells = getRandomCells($game_id);

        // сохраняем в сессии чтобы проводить вычисления
        $_SESSION['game_cells'] = $cells;

        echo json_encode(['status' => 'success', 'cells' => $cells]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
?>

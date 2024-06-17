<?php
    session_start();
    require 'db.php';

    // $conn = new PDO("mysql:host=localhost;dbname=game_db", "root", "");

    if (isset($_POST['game_id']) && isset($_POST['user_id'])) {
        $game_id = $_POST['game_id'];
        $user_id = $_POST['user_id'];

        $query = "SELECT player1_ready, player2_ready, player1_id, player2_id FROM games WHERE game_id = ?";
        $statement = $conn->prepare($query);
        $statement->bindParam(1, $game_id, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        try {
            if ($result['player1_id'] == $user_id && $result['player1_ready']) {
                $updateQuery = "UPDATE games SET player1_ready = 0, round = round + 0.5 WHERE game_id = ?";
            } else if ($result['player2_id'] == $user_id && $result['player2_ready']) {
                $updateQuery = "UPDATE games SET player2_ready = 0, round = round + 0.5 WHERE game_id = ?";
            }
            $updateStatement = $conn->prepare($updateQuery);
            $updateStatement->bindParam(1, $game_id, PDO::PARAM_STR);
            if ($updateStatement->execute()) {
                $response = array('status' => 'success', 'player1_id' => $result['player1_id']);
            } else {
                $response = array('status' => 'error', 'message' => 'Не удалось обновить состояние игры');
            }
        } catch (PDOException $e) {
            $response = ['status' => 'error', 'message' => 'Ошибка выполнения запроса: ' . $e->getMessage()];
        }

    }

    echo json_encode($response);
?>
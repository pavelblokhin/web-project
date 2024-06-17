<?php
    session_start();
    require 'db.php';

    $game_id = $_SESSION['game_id'];

    // $conn = new PDO("mysql:host=localhost;dbname=game_db", "root", "");

    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];

        $query = "SELECT player1_id, player2_id FROM games WHERE game_id = ?";
        $statement = $conn->prepare($query);
        $statement->bindParam(1, $game_id, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        // проверяем id игрока и говорим что он готов
        if ($result['player1_id'] == $user_id) {
            $updateQuery = "UPDATE games SET player1_ready = 1 WHERE game_id = ?";
        } else if ($result['player2_id'] == $user_id) {
            $updateQuery = "UPDATE games SET player2_ready = 1 WHERE game_id = ?";
        }

        $updateStatement = $conn->prepare($updateQuery);
        $updateStatement->bindParam(1, $game_id, PDO::PARAM_STR);
        $updateStatement->execute();
        
        echo json_encode([
           'status' => 'success'
        ]);
    } else {
        echo json_encode([
            'status' => 'error'
         ]);
    }
?>

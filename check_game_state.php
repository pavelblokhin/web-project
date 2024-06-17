<?php
    session_start();
    $game_id = $_SESSION['game_id'];
    $user_id = $_SESSION['user_id'];

    require 'db.php';
    // $conn = new PDO("mysql:host=localhost;dbname=game_db", "root", "");

    $query = "SELECT player1_ready, profit1, player2_ready, profit2, round FROM games WHERE game_id = ?";
    $statement = $conn->prepare($query);
    $statement->bindParam(1, $game_id, PDO::PARAM_STR);
    $statement->execute();
    $game_state = $statement->fetch(PDO::FETCH_ASSOC);

    $query2 = "SELECT player1_id, x1, y1, player2_id, x2, y2 FROM players_data WHERE game_id = ?";
    $statement2 = $conn->prepare($query2);
    $statement2->bindParam(1, $game_id, PDO::PARAM_STR);
    $statement2->execute();
    $cells = $statement2->fetch(PDO::FETCH_ASSOC);

       
    echo json_encode([
        'player1_ready' => $game_state['player1_ready'],
        'player2_ready' => $game_state['player2_ready'],
        'player1_id' => $cells['player1_id'],
        'player2_id' => $cells['player2_id'],
        'profit1' => $game_state['profit1'],
        'profit2' => $game_state['profit2'],
        'round' => $game_state['round'],
        'x1' => $cells['x1'],
        'y1' => $cells['y1'],
        'x2' => $cells['x2'],
        'y2' => $cells['y2']
    ]);
?>

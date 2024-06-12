<?php
    session_start();

    $conn = new PDO("mysql:host=localhost;dbname=game_db", "root", "");

    $game_id = $_POST['game_id'];
    $user_id = $_POST['user_id'];

    $query = "SELECT player1_id, player2_id FROM players_data WHERE game_id = ?";
    $statement = $conn->prepare($query);
    $statement->bindParam(1, $game_id, PDO::PARAM_STR);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);

    if ($result['player1_id'] == $user_id) {
        $query = "UPDATE players_data SET x1 = ?, y1 = ? WHERE game_id = ?";
    } else if ($result['player2_id'] == $user_id) {
        $query = "UPDATE players_data SET x2 = ?, y2 = ? WHERE game_id = ?";
    }

    if (isset($_POST['x']) && isset($_POST['y'])) {
        $x = $_POST['x'];
        $y = $_POST['y'];

        // Запрос к базе данных с использованием параметров
        // $query = "UPDATE players_data SET x = ?, y = ? WHERE game_id = ? AND player_id = ?";
        $statement = $conn->prepare($query);
        $statement->bindParam(1, $x, PDO::PARAM_INT);
        $statement->bindParam(2, $y, PDO::PARAM_INT);
        $statement->bindParam(3, $game_id, PDO::PARAM_STR);
        // $statement->bindParam(4, $user_id, PDO::PARAM_STR);

        // Выполнение запроса и проверка результата
        if ($statement->execute()) {
            $response = array('status' => 'success');
        } else {
            $response = array('status' => 'error', 'message' => 'Не удалось обновить данные.');
        }
    } else {
        $response = array('status' => 'error', 'message' => 'Координаты не выбраны.');
    }

    // Возврат ответа в формате JSON
    echo json_encode($response)
?>
<?php
    require_once 'class.php';
    session_start();
    $conn = new PDO("mysql:host=localhost;dbname=game_db", "root", "");

    if (isset($_POST['game_id']) && isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
        $game_id = $_POST['game_id'];
        $cells = $_SESSION['game_cells'];
        $player_point = $_SESSION['player_point'];
        $player_firm = unserialize($_SESSION['player_firm']);

        $cnt_buyers = 0;
        foreach ($cells as $cell) {
            $x_b = $cell['x'];
            $y_b = $cell['y'];
            $buyers = $cell['buyers'];

            // путь покупателя
            $way_b = abs($x_b - $player_point['x']) + abs($y_b - $player_point['y']);
            if ($way_b * 10 <= $player_firm->GetPrice()) {
                // клиент пошёл
                $cnt_buyers += 1;
            }
        }

        $player_firm->Buy($cnt_buyers);

        // проверяем айди игрока чтобы записать его данные в таблицу
        $query = "SELECT player1_id, player2_id FROM games WHERE game_id = ?";
        $statement = $conn->prepare($query);
        $statement->bindParam(1, $game_id, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result['player1_id'] == $user_id) {
            $query1 = "UPDATE games SET quantity1 = ?, money1 = ?, profit1 = profit1 + ? WHERE game_id = ?";
        } else if ($result['player2_id'] == $user_id) {
            $query1 = "UPDATE games SET quantity2 = ?, money2 = ?, profit2 = profit2 + ? WHERE game_id = ?";
        }
        $q = $player_firm->GetQuantity();
        $m = $player_firm->GetMoney();
        $p = $player_firm->GetProfit();


        $statement1 = $conn->prepare($query1);
        $statement1->bindParam(1, $q, PDO::PARAM_INT);
        $statement1->bindParam(2, $m, PDO::PARAM_INT);
        $statement1->bindParam(3, $p, PDO::PARAM_INT);
        $statement1->bindParam(4, $game_id, PDO::PARAM_STR);

        if ($statement1->execute()) {
            echo json_encode(['status' => 'success']);
            $_SESSION['player_firm'] = serialize($player_firm);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Не подсчитать прибыль.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Недостаточно данных.']);
    }

?>
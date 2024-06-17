<?php
    require_once 'class.php';
    require 'db.php';
    session_start();

    if (isset($_POST['game_id']) && isset($_POST['user_id'])) {
        $game_id = $_POST['game_id'];
        $cells = $_SESSION['game_cells'];
        $player1_firm = unserialize($_SESSION['player_firm']);


        $query1 = "SELECT player1_id, x1, y1, player2_id, x2, y2 FROM players_data WHERE game_id = ?";
        $statement1 = $conn->prepare($query1);
        $statement1->bindParam(1, $game_id, PDO::PARAM_STR);
        $statement1->execute();
        $result1 = $statement1->fetch(PDO::FETCH_ASSOC); // тут лежат клетки и айдишники

        $query2 = "SELECT price1, price2 FROM games WHERE game_id = ?";
        $statement2 = $conn->prepare($query2);
        $statement2->bindParam(1, $game_id, PDO::PARAM_STR);
        $statement2->execute();
        $result2 = $statement2->fetch(PDO::FETCH_ASSOC); // тут лежит цена

        $cnt1_buyers = 0;
        $cnt2_buyers = 0;
        foreach ($cells as $cell) {
            $x_b = $cell['x'];
            $y_b = $cell['y'];
            $buyers = $cell['buyers'];
            
            $way1 = abs($x_b - $result1['x1']) + abs($y_b - $result1['y1']);
            $way2 = abs($x_b - $result1['x2']) + abs($y_b - $result1['y2']);
            $cost1 = $way1 * 10 + $result2['price1'];
            $cost2 = $way2 * 10 + $result2['price2'];

            if ($cost1 < $cost2 && $cost1 <= 150) {
                $cnt1_buyers += $buyers;
            } else if ($cost2 < $cost1 && $cost2 <= 150) {
                $cnt2_buyers += $buyers;
            } else if ($cost1 == $cost2 && $cost1 <= 150) {
                $cnt1_buyers += floor($buyers / 2);
                $cnt2_buyers += floor($buyers / 2);
                if ($buyers % 2 == 1) {
                    if ($way1 <= $way2) {
                        $cnt1_buyers += 1;
                    } else {
                        $cnt2_buyers += 1;
                    }
                }
            }
        }

        $player1_firm->Buy($cnt1_buyers);

        $query = "UPDATE games SET quantity1 = ?, money1 = ?, buyers1 = ?, profit1 = profit1 + ?, buyers2 = ? WHERE game_id = ?";
        $q = $player1_firm->GetQuantity();
        $m = $player1_firm->GetMoney();
        $p = $player1_firm->GetProfit();


        $statement = $conn->prepare($query);
        $statement->bindParam(1, $q, PDO::PARAM_INT);
        $statement->bindParam(2, $m, PDO::PARAM_INT);
        $statement->bindParam(3, $cnt1_buyers, PDO::PARAM_INT);
        $statement->bindParam(4, $p, PDO::PARAM_INT);
        $statement->bindParam(5, $cnt2_buyers, PDO::PARAM_INT);
        $statement->bindParam(6, $game_id, PDO::PARAM_STR);

        if ($statement->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'count profit']);
            $_SESSION['player_firm'] = serialize($player1_firm);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Не подсчитать прибыль.']);
        }
    } else {
            echo json_encode(['status' => 'error', 'message' => 'Недостаточно данных.']);
        }

    // if (isset($_POST['game_id']) && isset($_POST['user_id'])) {
    //     $user_id = $_POST['user_id'];
    //     $game_id = $_POST['game_id'];
    //     $cells = $_SESSION['game_cells'];
    //     $player_point = $_SESSION['player_point'];
    //     $player_firm = unserialize($_SESSION['player_firm']);

    //     $cnt_buyers = 0;
    //     foreach ($cells as $cell) {
    //         $x_b = $cell['x'];
    //         $y_b = $cell['y'];
    //         $buyers = $cell['buyers'];

    //         // путь покупателя
    //         $way_b = abs($x_b - $player_point['x']) + abs($y_b - $player_point['y']);
    //         if ($way_b * 10 <= $player_firm->GetPrice()) {
    //             // клиент пошёл
    //             $cnt_buyers += 1;
    //         }
    //     }

    //     $player_firm->Buy($cnt_buyers);

    //     // проверяем айди игрока чтобы записать его данные в таблицу
    //     $query = "SELECT player1_id, player2_id FROM games WHERE game_id = ?";
    //     $statement = $conn->prepare($query);
    //     $statement->bindParam(1, $game_id, PDO::PARAM_STR);
    //     $statement->execute();
    //     $result = $statement->fetch(PDO::FETCH_ASSOC);

    //     if ($result['player1_id'] == $user_id) {
    //         $query1 = "UPDATE games SET quantity1 = ?, money1 = ?, profit1 = profit1 + ? WHERE game_id = ?";
    //     } else if ($result['player2_id'] == $user_id) {
    //         $query1 = "UPDATE games SET quantity2 = ?, money2 = ?, profit2 = profit2 + ? WHERE game_id = ?";
    //     }
    //     $q = $player_firm->GetQuantity();
    //     $m = $player_firm->GetMoney();
    //     $p = $player_firm->GetProfit();


    //     $statement1 = $conn->prepare($query1);
    //     $statement1->bindParam(1, $q, PDO::PARAM_INT);
    //     $statement1->bindParam(2, $m, PDO::PARAM_INT);
    //     $statement1->bindParam(3, $p, PDO::PARAM_INT);
    //     $statement1->bindParam(4, $game_id, PDO::PARAM_STR);

    //     if ($statement1->execute()) {
    //         echo json_encode(['status' => 'success']);
    //         $_SESSION['player_firm'] = serialize($player_firm);
    //     } else {
    //         echo json_encode(['status' => 'error', 'message' => 'Не подсчитать прибыль.']);
    //     }
    // } else {
    //     echo json_encode(['status' => 'error', 'message' => 'Недостаточно данных.']);
    // }

?>
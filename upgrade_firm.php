<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once 'class.php';
    require 'db.php';
    session_start();
    // $conn = new PDO("mysql:host=localhost;dbname=game_db", "root", "");

    

    if (isset($_POST['produce_value']) && isset($_POST['price_value']) && isset($_POST['user_id']) && isset($_POST['game_id'])) {
        $user_id = $_POST['user_id'];
        $game_id = $_POST['game_id'];
        $produceValue = $_POST['produce_value'];
        $priceValue = $_POST['price_value'];
        $upgradeCost = $_POST['upgrade_cost'];
        $player_firm = unserialize($_SESSION['player_firm']);

        if ($upgradeCost > 0) {
            $player_firm->UpGradeFunc($upgradeCost);
        }
        $player_firm->Produce($produceValue);
        $player_firm->SetPrice($priceValue);

        // проверяем айди игрока чтобы записать его данные в таблицу
        $query = "SELECT player1_id, player2_id FROM games WHERE game_id = ?";
        $statement = $conn->prepare($query);
        $statement->bindParam(1, $game_id, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result['player1_id'] == $user_id) {
            $query1 = "UPDATE games SET quantity1 = ?, money1 = ?, price1 = ?, cost1 = ? WHERE game_id = ?";
        } else if ($result['player2_id'] == $user_id) {
            $query1 = "UPDATE games SET quantity2 = ?, money2 = ?, price2 = ?, cost2 = ? WHERE game_id = ?";
        }
        $q = $player_firm->GetQuantity();
        $m = $player_firm->GetMoney();
        $p = $player_firm->GetPrice();
        $c = $player_firm->GetCost();

        $statement1 = $conn->prepare($query1);
        $statement1->bindParam(1, $q, PDO::PARAM_INT);
        $statement1->bindParam(2, $m, PDO::PARAM_INT);
        $statement1->bindParam(3, $p, PDO::PARAM_INT);
        $statement1->bindParam(4, $c, PDO::PARAM_INT);
        $statement1->bindParam(5, $game_id, PDO::PARAM_STR);

        if ($statement1->execute()) {
            echo json_encode(['status' => 'success']);
            $_SESSION['player_firm'] = serialize($player_firm);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Не удалось улучшить фирму.']);
        }

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Недостаточно данных.']);
    }

    // echo json_encode($response);

?>

<?php
    session_start();
    require 'db.php';

    if (isset($_POST['game_id']) && isset($_POST['user_id'])) {
        $game_id = $_POST['game_id'];
        $user_id = $_POST['user_id'];

        try {
        $query1 = "SELECT player1_id, player2_id FROM games WHERE game_id = ?";
        $statement1 = $conn->prepare($query1);
        $statement1->bindParam(1, $game_id, PDO::PARAM_STR);
        $statement1->execute();
        $result1 = $statement1->fetch(PDO::FETCH_ASSOC); // тут players id
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Ошибка выполнения запроса 1: ' . $e->getMessage()]);
        }
       


        if ($result1['player1_id'] == $user_id) {
            $query = "SELECT money1, cost1, price1, quantity1, profit1, profit2 FROM games WHERE game_id = ?";
        } else if ($result1['player2_id'] == $user_id) {
            $query = "SELECT money2, cost2, price2, quantity2, profit2, profit1 FROM games WHERE game_id = ?";
        }

        $statement = $conn->prepare($query);
        $statement->bindParam(1, $game_id, PDO::PARAM_STR);
        // $statement->execute();
        // $result = $statement->fetch(PDO::FETCH_ASSOC); 

        try {
            if ($statement->execute()) {
                $result = $statement->fetch(PDO::FETCH_ASSOC); 
                if ($result1['player1_id'] == $user_id) {
                    echo json_encode([
                        'status' => 'success',
                        'money' => $result['money1'],
                        'cost' => $result['cost1'],
                        'price' => $result['price1'],
                        'quantity' => $result['quantity1'],
                        'profit' => $result['profit1'],
                        'profit_opponent' => $result['profit2']
                    ]);
                } else if ($result1['player2_id'] == $user_id) {
                    echo json_encode([
                        'status' => 'success',
                        'money' => $result['money2'],
                        'cost' => $result['cost2'],
                        'price' => $result['price2'],
                        'quantity' => $result['quantity2'],
                        'profit' => $result['profit2'],
                        'profit_opponent' => $result['profit1']
                    ]);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'не удалось получить информацию о фирме']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Ошибка выполнения запроса 2: ' . $e->getMessage()]);
        }
        
        
    } else {
        echo json_encode(['status' => 'error', 'message' => 'smth wrong']);
    }
?>
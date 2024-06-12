<?php
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require 'vendor/autoload.php';
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    $conn = new PDO("mysql:host=localhost;dbname=game_db", "root", "");
    $key = '38efb7bb5e0eb5b7db47ac4d51b094e1cbc5bd7984402d2cc7616c2588aaa022';
    if (isset($_COOKIE['token'])) {
        $decoded = JWT::decode($_COOKIE['token'], new Key($key, 'HS256'));
    } else {
        header('location:index.php');
    }
    $user_id = $decoded->data->user_id;
    $game_id = uniqid();

    if (isset($_POST['game_id'])) {
        $game_id = $_POST['game_id'];
        $query = "SELECT * FROM games WHERE game_id = ?";
        $statement = $conn->prepare($query);
        $statement->bindParam(1, $game_id, PDO::PARAM_STR);
        $statement->execute();

        // проверяем что игра с нашем id создана
        if ($statement->rowCount() > 0) {
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            // проверяем что игра не заполнена
            if ($result['player2_id'] == 0) {
                $_SESSION['game_id'] = $game_id;
                $joinQuery = "UPDATE games SET player2_id = ? WHERE game_id = ?";
                $joinStatement = $conn->prepare($joinQuery);
                $joinStatement->bindParam(1, $user_id, PDO::PARAM_STR);
                $joinStatement->bindParam(2, $game_id, PDO::PARAM_STR);
                $joinStatement->execute();

                $query2 = "UPDATE players_data SET player2_id = ? WHERE game_id = ?";
                $statement2 = $conn->prepare($query2);
                $statement2->bindParam(1, $user_id, PDO::PARAM_STR);
                $statement2->bindParam(2, $game_id, PDO::PARAM_STR);
                $statement2->execute();
                
                // переходим на страницу игры
                header("Location:game.php?game_id=".$game_id);
            } else {
                echo"Игра заполнена";
            }
        } else {
            "Игра с таким ID не найдена.";
        }
    }
?>
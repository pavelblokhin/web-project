<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    session_start();

    require_once 'class.php';
    require 'db.php';
    require 'vendor/autoload.php';
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    // проверяем токен и подключаемся к бд
    // $conn = new PDO("mysql:host=localhost;dbname=game_db", "root", "");
    $key = '38efb7bb5e0eb5b7db47ac4d51b094e1cbc5bd7984402d2cc7616c2588aaa022';
    if (isset($_COOKIE['token'])) {
        $decoded = JWT::decode($_COOKIE['token'], new Key($key, 'HS256'));
    } else {
        header('location:index.php');
    }
    $user_id = $decoded->data->user_id;
    $game_id = uniqid();

    // создаём игру и закидываем данные в таблицу
    $query = "INSERT INTO games (game_id, player1_id, player1_ready) VALUES (?, ?, 1)";
    $statement = $conn->prepare($query);
    $statement->bindParam(1, $game_id, PDO::PARAM_STR);
    $statement->bindParam(2, $user_id, PDO::PARAM_STR);
    // $statement->bindParam(3, 1, PDO::PARAM_INT);
    $statement->execute();

    // данные для клеток
    $query2 = "INSERT INTO players_data (game_id, player1_id) VALUES (?, ?)";
    $statement2 = $conn->prepare($query2);
    $statement2->bindParam(1, $game_id, PDO::PARAM_STR);
    $statement2->bindParam(2, $user_id, PDO::PARAM_STR);
    $statement2->execute();

    $_SESSION['game_id'] = $game_id;
    $player_firm = new Firm(1000, 50);
    $_SESSION['player_firm'] = serialize($player_firm);
    // переходим на страницу игры
    header("location:game.php?game_id=".$game_id);
?>
<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    session_start();

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

    $query = "INSERT INTO games (game_id, player1_id) VALUES (?, ?)";
    $statement = $conn->prepare($query);
    $statement->bindParam(1, $game_id, PDO::PARAM_STR);
    $statement->bindParam(2, $user_id, PDO::PARAM_STR);
    $statement->execute();

    $query2 = "INSERT INTO players_data (game_id, player1_id) VALUES (?, ?)";
    $statement2 = $conn->prepare($query2);
    $statement2->bindParam(1, $game_id, PDO::PARAM_STR);
    $statement2->bindParam(2, $user_id, PDO::PARAM_STR);
    $statement2->execute();

    $_SESSION['game_id'] = $game_id;
    header("location:game.php?game_id=".$game_id);
?>
<?php
    session_start();
    $conn = new PDO("mysql:host=localhost;dbname=game_db", "root", "");

    $user_id = $_POST['user_id'];
    $upgrade_type = $_POST['upgrade_type'];

    if (isset($_POST['upgrade_type']) && isset($_POST['upgrade_value'])) {
        $upgradeType = $_POST['upgrade_type'];
        $upgradeValue = $_POST['upgrade_value'];
        $query = "UPDATE players_data SET $upgradeType = ? WHERE game_id = ? AND player_id = ?";
        $statement->bindParam(1, $upgradeValue, PDO::PARAM_INT);
        $statement->bindParam(2, $game_id, PDO::PARAM_STR);
        $statement->bindParam(3, $user_id, PDO::PARAM_STR);

        if ($statement->execute()) {
            $response = array('status' => 'success');
        } else {
            $response = array('status' => 'error', 'message' => 'Не удалось обновить данные.');
        }
    } else {
        $response = array('status' => 'error', 'message' => 'Координаты не выбраны.');
    }

    echo json_encode($response);

?>

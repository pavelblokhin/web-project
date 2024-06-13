<?php
    $game_id = $_GET['game_id'];
    session_start();

    require_once 'class.php';
    require 'vendor/autoload.php';
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    if (isset($_GET['game_id'])) {
        $_SESSION['game_id'] = $_GET['game_id'];
    }

    $conn = new PDO("mysql:host=localhost;dbname=game_db", "root", "");
    
    $key = '38efb7bb5e0eb5b7db47ac4d51b094e1cbc5bd7984402d2cc7616c2588aaa022';
    if (isset($_COOKIE['token'])) {
        $decoded = JWT::decode($_COOKIE['token'], new Key($key, 'HS256'));
    } else {
        header('location:index.php');
    }
    $user_id = $decoded->data->user_id;
    $_SESSION['user_id'] = $user_id;

    $cells = [];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Game</title>

    <style>
        .grid {
            display: grid;
            grid-template-columns: repeat(10, 50px);
            grid-template-rows: repeat(10, 50px);
            gap: 5px;
        }
        .cell {
            width: 50px;
            height: 50px;
            border: 1px solid #ccc;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
        }
        .cell.selected {
            background-image: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSjRqmZN2lqXOUsJFUKoB_EgY2mjQv9WEgyH_Uv91HTB5e5Xp-NPvxLpBbMosv9V5wuWUg&usqp=CAU'); /* Путь к вашему изображению */
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }
        .cell.player {
            background-color: blue;
        }
        .cell.opponent {
            background-color: red;
        }
        .cell.buyer {
            background-color: greenyellow;
        }
    </style>

    <script
    src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
    crossorigin="anonymous"></script>
</head>
    <body>
        <div class='container' style='width: 370px; margin-right: 0;'>
            <div class='card' >
            <div class="card-header">Информация по игре</div>
                <div class='card-body'>

                    <div class='row mb-3'>
                        <div class='col'>
                            <strong>Game ID:</strong>
                            <span><?php echo htmlspecialchars($game_id, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>                   
                    </div>

                    <div class='row mb-3'>
                        <div class='col'>
                            <strong>User ID:</strong>
                            <span><?php echo htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    </div>

                    <div class='row mb-3'>
                        <div class='col'>
                        <button onclick="window.location.href='welcome.php'">Выйти из игры</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div id="status"></div>
        <button id="readyButton" type="submit" class='readyButton'>Завершить ход</button>

        <h1>Игровое поле</h1>
        <div id="game-board" class="grid">
            <?php for ($x = 1; $x < 11; $x++): ?>
                <?php for ($y = 1; $y < 11; $y++): ?>
                    <div class="cell" data-x="<?php echo $x; ?>" data-y="<?php echo $y; ?>"></div>
                <?php endfor; ?>
            <?php endfor; ?>
        </div>
        <!-- Кнопка для подтверждения выбора клетки -->
        <button id="confirm-cell">Подтвердить выбор клетки</button>
        <!-- Поля ввода и кнопки для улучшения фирмы -->
        <div>
            <div>
                <input type="number" class="upgrade-value" id="upgrade-value-1" value="0">
                <button class="upgrade" data-type="1">Улучшить Фирму (Тип 1)</button>
            </div>
            <div>
                <input type="number" class="upgrade-value" id="upgrade-value-2" value="0">
                <button class="upgrade" data-type="2">Улучшить Фирму (Тип 2)</button>
            </div>
        </div>
        
        <!-- код для обработки состояния игры -->
        <script>
            $(document).ready(function() {
                // функция для проверки того что оба игрока готовы
                function checkGameState() {
                    $.ajax({
                        url: 'check_game_state.php',
                        method: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            console.log("Game State Response:", response);
                            if (response.player1_ready && response.player2_ready) {
                                $('#status').text('Оба игрока готовы. Начинаем новый раунд!');
                                updateCells(response);
                                setTimeout(function() {
                                    resetGameForNewRound();  // Сбрасываем состояние игры через 3 секунды
                                    if (<?php echo json_encode($user_id); ?> == response.player1_id) {
                                        generateAndSaveCells();
                                    } else if (<?php echo json_encode($user_id); ?> == response.player2_id) {
                                        setTimeout(function() {
                                            getAndDisplayCells();
                                        }, 5000);
                                    }
                                }, 3000);
                            } else if (response.player1_ready && !response.player2_ready) {
                                $('#status').text('Ожидание номер 2 игрока...');
                            } else if (!response.player1_ready && response.player2_ready) {
                                $('#status').text('Ожидание номер 1 игрока...');
                            }
                        }, 
                        error: function(xhr, status, error) {
                            console.error("Status: " + status);
                            console.error("Error: " + error);
                            console.error("Response Text: " + xhr.responseText);
                            alert('Произошла ошибка: ' + error);
                        }
                    });
                }

                // ставим интервал проверки 5 секунд
                setInterval(checkGameState, 2000);


                $('button.readyButton').on('click', function() {
                    $.ajax({
                        method: 'POST',
                        url: 'update_player_state.php',
                        data: { user_id: <?php echo json_encode($user_id); ?> },
                        success: function(response) {
                            let res = JSON.parse(response);
                            if (res.status === 'success') {
                                alert('Статус обновлен успешно!');
                            } else {
                                alert('Ошибка: ' + res.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Произошла ошибка: ' + error);
                        }
                    })
                });

                // Функция для сброса игры на новый раунд
                function resetGameForNewRound() {
                    $.ajax({
                        method: 'POST',
                        url: 'new_round.php',
                        data: { 
                            game_id: <?php echo json_encode($game_id); ?>,
                            user_id: <?php echo json_encode($user_id); ?>
                        },
                        success: function(response) {
                            console.log("Reset Game State Response:", response);
                            try {
                                let res = JSON.parse(response);
                                if (res.status === 'success') {
                                    cellConfirmed = false;
                                    select_cell = null;
                                    console.log(cellConfirmed);
                                    $('.cell').removeClass('selected');
                                    // updateCells(cells)
                                } else {
                                    alert('Ошибка: ' + res.message);
                                }
                            } catch (e) {
                                console.error("Parsing error:", e);
                                console.error("Response Text:", response);
                                alert('Ошибка парсинга ответа сервера');
                            }
                        }
                    });
                }
            });

            // функция для создания покупателей
            function generateAndSaveCells() {
                $.ajax({
                    url: 'generate_and_save_cells.php',
                    method: 'POST',
                    data: {
                        game_id: <?php echo json_encode($game_id); ?>,
                        action: 'generate'
                    },
                    success: function(response) {
                        let res = JSON.parse(response);
                        if (res.status === 'success') {
                            updateCellsWithBuyers(res.cells);
                        } else {
                            alert('Ошибка: ' + res.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Status: " + status);
                        console.error("Error: " + error);
                        console.error("Response Text: " + xhr.responseText);
                        alert('Произошла ошибка: ' + error);
                    }
                });
            }

            // функция для получения покупателей из таблицы
            function getAndDisplayCells() {
                $.ajax({
                    url: 'get_random_cells.php',
                    method: 'GET',
                    data: {
                        game_id: <?php echo json_encode($game_id); ?>
                    },
                    success: function(response) {
                        let res = JSON.parse(response);
                        if (res.status === 'success') {
                            updateCellsWithBuyers(res.cells);
                        } else {
                            alert('Ошибка: ' + res.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Status: " + status);
                        console.error("Error: " + error);
                        console.error("Response Text: " + xhr.responseText);
                        alert('Произошла ошибка: ' + error);
                    }
                });
            }

            // функция для расстановки покупателей по полю
            function updateCellsWithBuyers(buyers) {
                $('.cell').removeClass('buyer').text('');
                buyers.forEach(function(cell) {
                    $('.cell[data-x="' + cell.x + '"][data-y="' + cell.y + '"]').addClass('buyer').text(cell.buyers);
                });
            }
            

            // поля для клеток, улучишение фирмы и тд

            let select_cell = null; // выбранная клетка
            let cellConfirmed = false; // флаг для подтверждения клетки
            let last_cell = null;
            let last_oponent_cell = null;
            
            $('.cell').on('click', function() {
                if (!cellConfirmed) { // Проверка, что клетка еще не подтверждена
                    if (select_cell !== null) {
                        $('.cell[data-x="' + select_cell.x + '"][data-y="' + select_cell.y + '"]').removeClass('selected'); // Удаление класса у предыдущей выбранной клетки
                    }
                    $(this).addClass('selected'); // Добавление класса выбранной клетки
                    select_cell = { x: $(this).data('x'), y: $(this).data('y') }; // Сохранение координат выбранной клетки
                }
            });

            // Функция для обновления клеток
            function updateCells(response) {
                    // Очистка старого состояния клеток
                    $('.cell').removeClass('player opponent selected buyer');

                    if (response.player1_id === <?php echo json_encode($user_id); ?>) {
                        $('.cell[data-x="' + response.x1 + '"][data-y="' + response.y1 + '"]').addClass('player');
                        $('.cell[data-x="' + response.x2 + '"][data-y="' + response.y2 + '"]').addClass('opponent');
                        last_cell = {x: response.x1, y: response.y1};
                        last_oponent_cell = {x: response.x2, y: response.y2};
                    } else if (response.player2_id === <?php echo json_encode($user_id); ?>) {
                        $('.cell[data-x="' + response.x1 + '"][data-y="' + response.y1 + '"]').addClass('opponent');
                        $('.cell[data-x="' + response.x2 + '"][data-y="' + response.y2 + '"]').addClass('player');
                        last_cell = {x: response.x2, y: response.y2};
                        last_oponent_cell = {x: response.x1, y: response.y1};
                    }
                    
                    
                }

            $('#confirm-cell').on('click', function() {
                if (select_cell !== null) { // Проверка, что клетка выбрана
                    $.ajax({
                        method: 'POST',
                        url: 'select_cell.php', // Отправка запроса на сервер
                        data: {
                            game_id: <?php echo json_encode($game_id); ?>,
                            user_id: <?php echo json_encode($user_id); ?>, 
                            x: select_cell.x, 
                            y: select_cell.y 
                        },
                        success: function(response) {
                            console.log("Raw response:", response);
                            try {
                                let res = JSON.parse(response); // Обработка ответа сервера
                                console.log(res);
                                if (res.status === 'success') {
                                    cellConfirmed = true;
                                    alert('Клетка выбрана успешно!');
                                } else {
                                    alert('Ошибка: ' + res.message);
                                }
                            } catch (e) {
                                console.error("Parsing error:", e);
                                console.error("Response Text:", response);
                                alert('Ошибка парсинга ответа сервера');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Status: " + status);
                            console.error("Error: " + error);
                            console.error("Response Text: " + xhr.responseText);
                            alert('Произошла ошибка: ' + error);
                        }
                    });
                }
            });


            $('.upgrade').on('click', function() {
                let upgradeType = $(this).data('type'); // Получение типа улучшения
                let upgradeValue = $('#upgrade-value-${upgradeType}').val(); // Получение значения улучшения
                $.ajax({
                    method: 'POST',
                    url: 'upgrade_firm.php', // Отправка запроса на сервер для улучшения фирмы
                    data: { 
                        game_id: <?php echo json_encode($game_id); ?>,
                        user_id: <?php echo json_encode($user_id); ?>, 
                        upgrade_type: upgradeType,
                        upgrade_value: upgradeValue
                    },
                    success: function(response) {
                        let res = JSON.parse(response); // Обработка ответа сервера
                        if (res.status === 'success') {
                            alert('Фирма улучшена успешно!');
                        } else {
                            alert('Ошибка: ' + res.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Произошла ошибка: ' + error);
                    }
                });
            });

        </script>
    </body>
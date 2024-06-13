<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require 'vendor/autoload.php';
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    $key = '38efb7bb5e0eb5b7db47ac4d51b094e1cbc5bd7984402d2cc7616c2588aaa022';

    // проверяем токен
    if (isset($_COOKIE['token'])) {
        $decoded = JWT::decode($_COOKIE['token'], new Key($key, 'HS256'));
    } else {
        header('location:index.php');
    }
?>

<!doctype html>
<html lang="en">
  	<head>
    	<!-- Required meta tags -->
    	<meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1">

    	<!-- Bootstrap CSS -->
    	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    	<title>Hotteling Game</title>
  	</head>
  	<body>
    	<div class="container">
    		<h1 class="text-center mt-5 mb-5">Hotteling Game</h1>
    		<div class="row">
    			<div class="col-md-4">&nbsp;</div>
    			<div class="col-md-4 text-center">
    				<h1>Welcome <b><?php echo $decoded->data->name; ?></b></h1>
    				<a href="logout.php">Logout</a>
    				
		    	</div>
	    	</div>
    	</div>
  	</body>
</html>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

        <title>Game</title>
    </head>
    <body>
        <div class='container'> 
            <div class='card'>
            <div class="card-header">Начнём игру</div>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col'>
                                <div class='mb-3'>
                                    <label class='form-label'>Введите id игры</label>
                                    <div>
                                    <form id=JoinForm method='post' action='join_game.php'>
                                        <input type="text" class="form-control-sm" name="game_id">
                                        <button class="btn btn-primary" type="submit">Join</button>
                                    </form>
                                    </div>
                                </div>                               
                            </div>
                            <div class='col'>
                                <label class='form-label'>Создать игру (после создания появится id игры)</label>
                                <div>
                                <form id=CreateGame method='post' action='create_game.php'>
                                    <button class="btn btn-primary" type="submit">Create</button>
                                </form>
                                </div>                             
                            </div>
                        </div>
                    </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>


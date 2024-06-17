<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require 'db.php';
    require 'vendor/autoload.php';
    use \Firebase\JWT\JWT;
    use \Firebase\JWT\Key;

    $key = '38efb7bb5e0eb5b7db47ac4d51b094e1cbc5bd7984402d2cc7616c2588aaa022';

    $message = '';
    $error = '';

    // вход в аккаунт
    if (isset($_POST['login'])) {
        // $conn = new mysqli("localhost", "root", "", "users");

        if(empty($_POST["email"])){
            $error = 'Please Enter Email';
        } else if(empty($_POST["password"])){
            $error = 'Please Enter Password';
        } else {
            $query = "SELECT * FROM user_data WHERE email = ?";
            $statement = $conn->prepare($query);
            $statement->execute([$_POST['email']]);
            $data = $statement->fetch(PDO::FETCH_ASSOC);

            // создание токена на час
            if ($data) {
                if (password_verify($_POST['password'], $data['password'])) {
                    
                    $token = JWT::encode(
                        array(
                            'iat'  => time(),
                            'nbf'  => time(),
                            'exp'  => time() + 3600,
                            'data' => array(
                                'user_id'	=>	$data['user_id'],
                                'email'	=>	$data['email'],
                                'name'  =>  $data['name']
                            )
                        ),
                        $key,
                        'HS256'
                    );
                    setcookie("token", $token, time() + 3600, "/", "", true, true);
                    header('location:welcome.php');
                } else {
                    $error = "Wrong password";
                }
            } else {
                $error = "Wrong email or you have not logined";
            }
        }
    }
?>


<!doctype html>
<html lang="en">
  	<head>
    	<meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1">

    	<!-- Bootstrap CSS -->
    	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    	<title>Login</title>
  	</head>
  	<body>
    	<div class="container">
    		<h1 class="text-center mt-5 mb-5">Login</h1>
    		<div class="row">
    			<div class="col-md-4">&nbsp;</div>
    			<div class="col-md-4">
    				<?php

    				if($error !== '')
    				{
    					echo '<div class="alert alert-danger">'.$error.'</div>';
    				}

    				?>
		    		<div class="card">
		    			<div class="card-header">Login</div>
		    			<div class="card-body">
		    				<form method="post">
		    					<div class="mb-3">
			    					<label>Email</label>
			    					<input type="email" name="email" class="form-control" />
			    				</div>
			    				<div class="mb-3">
			    					<label>Password</label>
			    					<input type="password" name="password" class="form-control" />
			    				</div>
			    				<div class="text-center">
			    					<input type="submit" name="login" class="btn btn-primary" value="Login" />
                                    <a href="register.php"><input type="button" name="signup" class="btn btn-link" value="Sing up"></a> <!-- переход на страницу регистрации  -->
			    				</div>
		    				</form>
		    			</div>
		    		</div>
		    	</div>
	    	</div>
    	</div>
  	</body>
</html>
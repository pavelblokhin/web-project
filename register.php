<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

    require 'vendor/autoload.php';

    use Firebase\JWT\JWT;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    $error = '';
    $message = '';

    if (isset($_POST['register'])) {
        $conn = new PDO('mysql:dbname=users;host=localhost', 'root', '');

        // проверяем ввёл ли пользователь email
        if (empty($_POST['email'])) {
            $error = 'Please enter the email';
        // проверяем заполнил ли пароль
        } else if (empty($_POST['password'])) {
            $error = 'Please enter the password';
		// заполнил ли поле имя
        } else if (empty($_POST['name'])) {
			$error = 'Please enter your name';
		// проверяем был ли зарегестрирован пользователь уже
		} else {
            $query = "SELECT user_id FROM user_data WHERE email = ?";
            $statement = $conn->prepare($query);
            $statement->execute([$_POST["email"]]);
            // пользователь зарегестрирован
            if($statement->rowCount() > 0) {
			    $error = 'Email alaready exists';
            // регестрируем пользователя
		    } else {
                $data = array(
					':name'        => 	trim($_POST['name']),
                    ':email'       =>	trim($_POST['email']),
				    ':password'    =>	trim($_POST['password']),
                    ':user_id'     =>   trim(md5(microtime(true)))
                );
                $InsertQuery = "INSERT INTO user_data (user_id, name, email, password) VALUES (:user_id, :name, :email, :password)";
			    $statement = $conn->prepare($InsertQuery);
                if ($statement->execute($data)) {
                    $key = '38efb7bb5e0eb5b7db47ac4d51b094e1cbc5bd7984402d2cc7616c2588aaa022';

                    $payload = array(
                        'email'		=>	trim($_POST['email'])
                    );
    
                    // $token = JWT::encode($payload, $key, 'HS256');
					header('location:index.php');
                }


            }
        }

        
        
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

    	<title>Registration</title>
  	</head>
  	<body>
    	<div class="container">
    		<h1 class="text-center mt-5 mb-5">Registration Form</h1>
    		<div class="row">
    			<div class="col-md-4">&nbsp;</div>
    			<div class="col-md-4">
    				<?php

    				if($error !== '')
    				{
    					echo '<div class="alert alert-danger">'.$error.'</div>';
    				}

    				if($message !== '')
    				{
    					echo '<div class="alert alert-success">'.$message.'</div>';
    				}

    				?>
		    		<div class="card">
		    			<div class="card-header">Register</div>
		    			<div class="card-body">
		    				<form method="post">
                            <div class="mb-3">
			    					<label>Name</label>
			    					<input type="text" name="name" class="form-control" />
			    				</div>
			    				<div class="mb-3">
			    					<label>Email</label>
			    					<input type="email" name="email" class="form-control" />
			    				</div>
			    				<div class="mb-3">
			    					<label>Password</label>
			    					<input type="password" name="password" class="form-control" />
			    				</div>
			    				<div class="text-center">
			    					<input type="submit" name="register" value="Sign in" class="btn btn-primary" />
			    				</div>
		    				</form>
		    			</div>
		    		</div>
		    	</div>
	    	</div>
    	</div>
  	</body>
</html>
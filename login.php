<?php 
	require 'db_connect.php';

	session_start();
	
	// json response array
	$response = array("error" => FALSE);

	if(isset($_POST['login'])) {

		$username = $_POST['username'];
		$password = $_POST['password'];

		/*find if username exists in the users table*/
		$query = 'select count(*) as count from users where username = ?';
		if($stmt = mysqli_prepare($conn, $query)){

			// bind parameters for markers
	    	mysqli_stmt_bind_param($stmt, "s", $username);

			// execute statement
			mysqli_stmt_execute($stmt);

			// bind result variables
			mysqli_stmt_bind_result($stmt, $count);

			// fetch values , use loop to get multiple column values
			mysqli_stmt_fetch($stmt);

			// close statement
			mysqli_stmt_close($stmt);	
			
			if($count > 0){

				/*find salt and password from users table for this user*/
				$query = 'select user_id, password, salt from users where username = ?';
				if($stmt = mysqli_prepare($conn, $query)){
					mysqli_stmt_bind_param($stmt, "s", $username);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_bind_result($stmt, $user_id, $hashcode, $salt);
					mysqli_stmt_fetch($stmt);
					mysqli_stmt_close($stmt);
				}
				else{
					echo 'mysql_prepare for select password, salt from users failed';
					exit();
				}

				/*create hash for input password*/
				$options = [
		    		'cost' => 10,
		  			'salt' => $salt,
				];
				$hash = password_hash($password, PASSWORD_BCRYPT, $options);

				/*verify if the hash values for the passwords match*/
				if($hash == $hashcode){
					echo 'Congrats, you will be logged in';
					$response['error'] = FALSE;
					$response['user_id'] = $user_id;
					$_SESSION['username'] = $username;
					echo json_encode($response);
				}
				else{
					$response['error'] = TRUE;
					$response['error_message'] = 'Invalid password';
					echo json_encode($response);
					exit();
				}
			}
			else{
				$response['error'] = TRUE;
				$response['error_message'] = 'Invalid username';
				echo json_encode($response);
				exit();
			}
		}
		else{
			echo 'mysqli_prepare for select count(*) from users failed';
			exit();
		}

		/* close connection */
		mysqli_close($conn);
	}
	else{
		// required post params is missing
	    $response["error"] = TRUE;
	    $response["error_msg"] = "Required parameters username or password is missing!";
	    echo json_encode($response);
	}
?>
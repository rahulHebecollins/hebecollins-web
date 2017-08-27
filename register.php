<?php
require 'conf/db_connect.php';
/*this page does managers' registration after email was verified-----------------------------------------------*/
if(isset($_GET['c'])){
    $hash = htmlspecialchars($_GET['c']);

    /*finds the number of records having this hash value*/
    $query = 'select count(*) as count from cache where hash = ?';

    if($stmt = mysqli_prepare($conn, $query)){

        // bind parameters for markers
        mysqli_stmt_bind_param($stmt, "s", $hash);

        // execute statement
        mysqli_stmt_execute($stmt);

        // bind result variables
        mysqli_stmt_bind_result($stmt, $count);

        // fetch values , use loop to get multiple column values
        mysqli_stmt_fetch($stmt);

        // close statement
        mysqli_stmt_close($stmt);

        if($count > 0){
            /*get the record where hash matched*/
            $query = 'select * from cache where hash = ?';

            if($stmt = mysqli_prepare($conn, $query)){

                mysqli_stmt_bind_param($stmt, "s", $hash);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $email, $fname, $mname, $lname, $gym_name, $dob, $gender, $latitude, $longitude, $street, $locality, $city, $pin, $state, $country, $timestamp, $hashcode);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);

                /*delete the record now from cache*/
                $query = 'delete from cache where hash = ?';
                if($stmt = mysqli_prepare($conn, $query)){
                    mysqli_stmt_bind_param($stmt, "s", $hash);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);

                    /*create a form letting user set his login password*/
                    echo '<!DOCTYPE html>
								<html>
									<head>
										<title>User registration</title>
									</head>

									<body>
										<p>Hi <b>'.$fname.' '.$mname.' '.$lname.'</b>.<br> Your username is <i>'.$email.'</i>. Please set your password for future logins.<p><br>
										<p><b>Please, do not refresh this page else, you won\'t be able to register.</b></p>
										<form action="register.php" method="post" autocomplete="off">
											<div class="div-table">
												<div class="div-table-row">
													<div class="div-table-col">Password:</div>
													<div class="div-table-col"><input id="pwd" type="password" name="pwd" onkeyup="pass_check();" required></div>
													<div class="div-table-col"><input type="hidden" name="email" value='.$email.'></div>
													<div class="div-table-col"><input type="hidden" name="fname" value='.$fname.'></div>
													<div class="div-table-col"><input type="hidden" name="mname" value='.$mname.'></div>
													<div class="div-table-col"><input type="hidden" name="lname" value='.$lname.'></div>
													<div class="div-table-col"><input type="hidden" name="gym_name" value='.$gym_name.'></div>
													<div class="div-table-col"><input type="hidden" name="dob" value='.$dob.' readonly></div>
													<div class="div-table-col"><input type="hidden" name="gender" value='.$gender.'></div>
													<div class="div-table-col"><input type="hidden" name="latitude" value='.$latitude.'></div>
													<div class="div-table-col"><input type="hidden" name="longitude" value='.$longitude.'></div>
													<div class="div-table-col"><input type="hidden" name="street" value='.$street.'></div>
													<div class="div-table-col"><input type="hidden" name="locality" value='.$locality.'></div>
													<div class="div-table-col"><input type="hidden" name="city" value='.$city.'></div>
													<div class="div-table-col"><input type="hidden" name="pin" value='.$pin.'></div>
													<div class="div-table-col"><input type="hidden" name="state" value='.$state.'></div>
													<div class="div-table-col"><input type="hidden" name="country" value='.$country.'></div>
												</div>
												<div class="div-table-row">
													<div class="div-table-col">Confirm password:</div>
													<div class="div-table-col"><input id="confirm_pwd" type="password" name="confirm_pwd" onkeyup="pass_check();" required><span id="message"></span></div>
												</div>
												<div class="div-table-row">
													<div class="div-table-col"><input type="submit" name="register" value="Register"></div>
												</div>
											</div>
										</form>

										<script type="text/javascript">
											var pass_check = function(){
												if(document.getElementById("pwd").value == document.getElementById("confirm_pwd").value){
												    document.getElementById("message").style.color = "green";
												    document.getElementById("message").innerHTML = "matching";
												}
												else{
													document.getElementById("message").style.color = "red";
												    document.getElementById("message").innerHTML = "not matching";
												}
											}
										</script>
									</body>
								</html>';
                }
                else{
                    echo 'mysqli_prepare for delete from caache failed';
                    exit();
                }
            }
            else{
                echo 'mysqli_prepare for select * from cache failed';
                exit();
            }
        }
        else{
            echo 'The verification link sent to your mail must have expired, or you are deliberately trying to sabotage this neat little venture of ours. Either case, shame on you!';
            exit();
        }
    }
    else{
        echo 'mysqli_prepare for select count(*) from cache failed';
        exit();
    }
}
else if(isset($_POST['register'])){
    $pwd = $_POST['pwd'];
    /*verify that passwords match*/
    $email = htmlspecialchars($_POST['email']);
    $fname = htmlspecialchars($_POST['fname']);
    $mname = htmlspecialchars($_POST['mname']);
    $lname = htmlspecialchars($_POST['lname']);
    $gym_name = htmlspecialchars($_POST['gym_name']);
    $dob = htmlspecialchars($_POST['dob']);
    $gender = htmlspecialchars($_POST['gender']);
    $latitude = htmlspecialchars($_POST['latitude']);
    $longitude = htmlspecialchars($_POST['longitude']);
    $street = htmlspecialchars($_POST['street']);
    $locality = htmlspecialchars($_POST['locality']);
    $city = htmlspecialchars($_POST['city']);
    $state = htmlspecialchars($_POST['state']);
    $country = htmlspecialchars($_POST['country']);
    $pin = htmlspecialchars($_POST['pin']);

    $salt = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
    $options = [
        'cost' => 10,
        'salt' => $salt,
    ];
    $hash = password_hash($pwd, PASSWORD_BCRYPT, $options);

    /*Enter new user entry into users table*/
    $uid = uniqid().'';
    $m_id = $uid.'MN';
    $query = 'insert into users (user_id, username, password, salt) values (?, ?, ?, ?)';
    if($stmt = mysqli_prepare($conn, $query)){
        mysqli_stmt_bind_param($stmt, "ssss", $m_id, $email, $hash, $salt);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        /*Enter details into gym table*/
        $query = 'insert into gyms (gym_name, street, locality, city, state, country, pin, latitude, longitude) values (?, ?, ?, ?, ?, ?, ?, ?, ?)';
        if($stmt = mysqli_prepare($conn, $query)){
            mysqli_stmt_bind_param($stmt, "sssssssdd", $gym_name, $street, $locality, $city, $state, $country, $pin, $latitude, $longitude);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        else{
            echo 'mysqli_prepare for insert into gyms failed';
            exit();
        }

        /*get the corresponding gym_id for use in next query*/
        $query = 'select gym_id from gyms where gym_name = ?';
        if($stmt = mysqli_prepare($conn, $query)){
            mysqli_stmt_bind_param($stmt, "s", $gym_name);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $gym_id);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }
        else{
            echo 'mysqli_prepare for select gym_id from gyms failed';
            exit();
        }

        /*Enter details into managers table*/
        $query = 'insert into managers (m_id, username, fname, mname, lname, gym_id, gender) values (?, ?, ?, ?, ?, ?, ?)';
        if($stmt = mysqli_prepare($conn, $query)){
            mysqli_stmt_bind_param($stmt, "sssssis", $uid, $email, $fname, $mname, $lname, $gym_id, $gender);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        else{
            echo 'mysqli_prepare for insert into managers failed';
            exit();
        }

        /*Enter date of birth in date_of_birth table*/
        $query = 'insert into date_of_birth (user_id, dob) values (?, ?)';
        if($stmt = mysqli_prepare($conn, $query)){
            mysqli_stmt_bind_param($stmt, "ss", $m_id, $dob);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        else{
            echo 'mysqli_prepare for insert into date_of_birth failed';
            exit();
        }

        echo 'You have been registered. Redirecting...';
        header( "refresh:2; url = html/signup.html" );
    }
    else{
        echo 'mysqli_prepare for insert into users failed';
        exit();
    }
}
?>

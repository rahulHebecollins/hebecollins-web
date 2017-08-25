<?php

//mysqli_report(MYSQLI_REPORT_ALL);
require 'conf/db_connect.php';
if(isset($_POST['register'])){

    $email = htmlspecialchars($_POST['email']);
    /*verify the email using regular expression-----------------------------------------------------------------*/
    if(filter_var($email, FILTER_VALIDATE_EMAIL)){

        /* prepare and bind statements-------------------------------------------------------------------------*/
        $query = 'select count(*) as count from managers where username = ?';


        if($stmt = mysqli_prepare($conn, $query)){

            // bind parameters for markers
            mysqli_stmt_bind_param($stmt, "s", $email);

            // execute statement
            mysqli_stmt_execute($stmt);

            // bind result variables
            mysqli_stmt_bind_result($stmt, $count);

            // fetch values , use loop to get multiple column values
            mysqli_stmt_fetch($stmt);

            // close statement
            mysqli_stmt_close($stmt);

            if($count > 0){
                echo 'Error! The email is already registered.';
            }
            else{
                $fname = htmlspecialchars($_POST['fname']);
                $mname = htmlspecialchars($_POST['mname']);
                $lname = htmlspecialchars($_POST['lname']);
                $gym_name = htmlspecialchars($_POST['gym_name']);
                $dob = htmlspecialchars($_POST['dob']);
                $gender = htmlspecialchars($_POST['gender']);
                $latitude = htmlspecialchars($_POST['latitude']);
                if(empty($latitude))
                    $latitude = 100.0;
                $longitude = htmlspecialchars($_POST['longitude']);
                if(empty($longitude))
                    $longitude = 200.0;
                $street = htmlspecialchars($_POST['street']);
                $locality = htmlspecialchars($_POST['locality']);
                $city = htmlspecialchars($_POST['city']);
                $state = htmlspecialchars($_POST['state']);
                $country = htmlspecialchars($_POST['country']);
                $pin = htmlspecialchars($_POST['pin']);

                $options = [
                    'cost' => 10,
                    'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
                ];
                $hash = password_hash($email, PASSWORD_BCRYPT, $options);

                /*Insert into cache, records stay for not more than 10 minutes---------------------------------*/
                $query = 'insert into cache (email, fname, mname, lname, gym_name, dob, gender, latitude, longitude, street, locality, city, pin, state, country, hash) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                if($stmt = mysqli_prepare($conn, $query)){
                    mysqli_stmt_bind_param($stmt, "sssssssddsssssss", $email, $fname, $mname, $lname, $gym_name, $dob, $gender, $latitude, $longitude, $street, $locality, $city, $pin, $state, $country, $hash);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);

                    /*send encrypted registration link to mail using PHPMailer----------------------------------*/
                    require 'PHPMailer-master/PHPMailerAutoload.php';

                    $mail = new PHPMailer;

                    //$mail->SMTPDebug = 1;                                 // Enable verbose debug output

                    $mail->isSMTP();                                      // Set mailer to use SMTP
                    $mail->Host = 'smtp.gmail.com';  		     // Specify main and backup SMTP servers
                    $mail->SMTPAuth = true;                             // Enable SMTP authentication
                    $mail->Username = 'sender mail';      		   // SMTP username
                    $mail->Password = 'sender mail password';         // SMTP password
                    $mail->SMTPSecure = 'ssl';                       // Enable TLS encryption, `ssl` also accepted
                    $mail->Port = 465;                              // TCP port to connect to

                    $mail->setFrom('sender mail', 'sender name');
                    $mail->addAddress($email, $fname.' '.$mname.' '.$lname);      	  // Add a recipient
                    $mail->addReplyTo($email, 'Customer Service');

                    $mail->isHTML(true);                                  		// Set email format to HTML

                    $mail->Subject = 'Hebecollins- Email verification.';
                    $mail->Body    = '<html>
											<head>
											  <title>Email Verification</title>
											</head>
											<body>
											  <p>Please click the following link to proceed with the registration</p>
											  <a href="www.hebecollins.com/register.php?c='.$hash.'">www.hebecollins.com/register.php?c='.$hash.'</a>
											</body>
										</html>';
                    $mail->AltBody = 'Goto this link to verify: www.hebecollins.com/register.php?c='.$hash;

                    if(!$mail->send()) {
                        echo 'Message could not be sent.';
                        echo 'Mailer Error: ' . $mail->ErrorInfo;
                    } else {
                        echo 'A verification mail has been sent to your email account. The link expires in 10 minutes.';
                    }
                }
                else{
                    echo 'mysqli_prepare for insert into cache failed';
                    exit();
                }
            }
        }
        else{
            echo 'mysqli_prepare for select count(*) from managers failed';
            exit();
        }
    }
    else{
        echo 'Invalid email';
        exit();
    }

    /* close connection */
    mysqli_close($conn);
}
?>

<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;
//get all users
$app -> get('/api/customers', function($request, $response){
    $sql = "SELECT * FROM user";
    try{
        //get DB object
        $db = new db();
        // calling connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($users);
    }catch(PDOException $e){
        echo '{'.$e->getMessage().'}';
    };
} );

//get single user
$app -> get('/api/customer/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "SELECT * FROM user WHERE user_id = '$id'";
    try{
        //get DB object
        $db = new db();
        // calling connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($users);
    }catch(PDOException $e){
        echo '{'.$e->getMessage().'}';
    };
} );

//add a user
$app -> post('/api/customer/add', function(Request $request, Response $response){
    $user_id = $request->getParam('user_id');
    $username = $request->getParam('username');
    $password = $request->getParam('password');
    $salt = $request->getParam('salt');

    $sql = "INSERT INTO user (user_id,username,password,salt) 
            VALUES (:user_id,:username,:password,:salt)";
    try{
        //get DB object
        $db = new db();
        // calling connect

        $db = $db->connect();
        $stmt= $db->prepare($sql);
        $stmt->bindParam(':user_id',$user_id);
        $stmt->bindParam('username',$username);
        $stmt->bindParam('password',$password);
        $stmt->bindParam('salt',$salt);

        $stmt->execute();
        echo 'user added';

    }catch(PDOException $e){
        echo '{'.$e->getMessage().'}';
    };
} );


//update a user
$app -> put('/api/customer/update/{id}', function(Request $request, Response $response){
    $id = $request ->getAttribute('id');
    $user_id = $request->getParam('user_id');
    $username = $request->getParam('username');
    $password = $request->getParam('password');
    $salt = $request->getParam('salt');

    $sql = "UPDATE user SET 
                        user_id = :user_id,
                        username = :username,
                        password = :password,
                        salt = :salt
                    WHERE user_id = '$id'";
    try{
        //get DB object
        $db = new db();
        // calling connect

        $db = $db->connect();
        $stmt= $db->prepare($sql);
        $stmt->bindParam(':user_id',$user_id);
        $stmt->bindParam('username',$username);
        $stmt->bindParam('password',$password);
        $stmt->bindParam('salt',$salt);

        $stmt->execute();
        echo 'user updated ';

    }catch(PDOException $e){
        echo '{'.$e->getMessage().'}';
    };
} );


//delete a user
//get single user

$app -> delete('/api/customer/delete/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM user WHERE user_id = '$id'";
    try{
        //get DB object
        $db = new db();
        // calling connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo 'customer deleted';
    }catch(PDOException $e){
        echo '{'.$e->getMessage().'}';
    };
} );

$app -> get('/api/user/demo', function($request, $response){
//    require_once  '../../User.php';
    $user =  new User();
    echo $user->deleteUser();
});

$app -> get('/api/user/{id}', '\User:deleteUser');

$app -> get('/api/customers/demo', function($request, $response) {
//    $sql = "SELECT * FROM user";
//    try{
//        //get DB object
//        $db = new db();
//        // calling connect
//        $db = $db->connect();
//        $stmt = $db->query($sql);
//        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
//        $db = null;
//        echo json_encode($users);
//    }catch(PDOException $e){
//        echo '{'.$e->getMessage().'}';
//    };
echo "something else";
});
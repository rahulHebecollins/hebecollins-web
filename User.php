<?php

class User{

    public function deleteUser($request,$response){

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
    }
}
/**
 * Created by PhpStorm.
 * User: avinash
 * Date: 27-08-2017
 * Time: 10:03
 */
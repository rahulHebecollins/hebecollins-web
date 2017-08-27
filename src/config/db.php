<?php
    class db{
        //properties
        private $dbhost = '127.0.0.1';
        private $dbuser = 'root';
        private $dbpass = '';
        private $dbname = 'authentication';

        //connect
        public function connect(){
            $mysql_connect_str = "mysql:host=$this->dbhost;dbname=$this->dbname;";
            $dbConnection = new PDO($mysql_connect_str, $this->dbuser, $this->dbpass);
                // http://php.net/manual/en/pdo.connections.php
            $dbConnection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            return $dbConnection;
        }
    }


//$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
//if($conn->connect_error){
//    die("Connection failed: " . $conn->connect_error);
//}
?>
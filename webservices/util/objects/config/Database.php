<?php
class Database{
	private $host = "localhost";
	private $db_name = "konstant707376_UniPoll";
	private $username = "konstant707376_UniPoll";
	private $password = "22920276324story";
	private $conn;
	
	public function __construct(){
  			$this->conn = new PDO("mysql:host=$this->host;dbname=$this->db_name", $this->username, $this->password,
        						   array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
			$this->conn->exec("SET CHARACTER SET 'utf8'"); 
  			// set the PDO error mode to exception
  			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	public function getConnection(){
		return $this->conn;
	}
	
	public function closeConnection(){
		$this->conn = null;
	}

}


?>
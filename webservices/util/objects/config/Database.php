<?php
class Database{
	private $host = "localhost";
	private $db_name = "konstant707376_UniPoll";
	private $username = "konstant707376_UniPoll";
	private $password = "22920276324story";
	private $conn;
	
	public function __construct(){
		try {
  			$this->conn = new PDO("mysql:host=$this->host;dbname=$this->db_name", $this->username, $this->password);
  			// set the PDO error mode to exception
  			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  			echo "Connected successfully";
		} catch(PDOException $e) {
 			 echo "Connection failed: " . $e->getMessage();
		}
	}
	
	public function getConnection(){
		return $this->conn;
	}
	
	public function closeConnection(){
		$this->conn = null;
	}

}


?>
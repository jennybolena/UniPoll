<?php

header("Content-type:application/json");
include_once 'config/Database.php';
class Vote
{

    private $option_id;
    private $poll_id;
    private $user_id;
    private $conn;


    public function __construct($conn, $poll_id, $option_id, $user_id)
    {
        $this->conn = $conn;
        $this->user_id = $user_id;
        $this->poll_id = $poll_id;
        $this->option_id = $option_id;
    }


    public function addVote()
    {
        $sql = "INSERT INTO Vote (optionId, pollId, userId)
		VALUES (:option_id, :poll_id, :user_id)";
		
            $stmt = $this->conn->prepare($sql);
			$stmt->bindParam(':user_id', $this->user_id);
			$stmt->bindParam(':poll_id', $this->poll_id);
			$stmt->bindParam(':option_id',$this->option_id);
            $stmt->execute();
    }

    public function updateVote()
    {
        $sql = "UPDATE Vote 
                SET optionId = :option_id
                WHERE pollId = :poll_id AND userId = :user_id";

            $stmt = $this->conn->prepare($sql);
			$stmt->bindParam(':user_id', $this->user_id);
			$stmt->bindParam(':poll_id', $this->poll_id);
			$stmt->bindParam(':option_id', $this->option_id);
            $stmt->execute();

    }


   /* public function getVote()
    {
        $sql = "SELECT * from Vote 
                where userId = $this->user_id AND pollId = $this->poll_id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $num = $stmt->rowCount();

            $row_item = array();
            if($num == 1){
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                extract($row);
                $row_item=array(
                    "status" => 1,
                    "poll_id" => $pollId,
                    "user_id" => $userId,
                    "option_id" => $optionId
                );
            }else{
                $row_item=array(
                    "status" => 0,
                    "msg" => "user hasn't voted for this poll"
                );
            }

            echo json_encode($row_item);
        } catch (Exception $e) {
            echo "error" . $e->getMessage();
        }
    }*/
}
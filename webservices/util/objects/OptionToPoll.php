<?php

header("Content-type:application/json");
include_once 'config/Database.php';
class OptionToPoll
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function addOptionsToPoll($group_id, $poll_id, $options){
        $j = 1;
        foreach($options as &$value){
            $sql = "INSERT INTO OptionToPoll (optionId, pollId, _option)
					VALUES ($j, $poll_id, $value)";

            try {
                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
                $j = $j + 1;
                echo "value added";
            } catch (Exception $e) {
                echo "error" . $e->getMessage();
            }
        }
    }


}

?>
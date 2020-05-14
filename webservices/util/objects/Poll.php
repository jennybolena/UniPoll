<?php

header("Content-type:application/json");
include_once 'config/Database.php';
include_once 'OptionToPoll.php';
class Poll
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function addPoll($group_id, $_question, $end_time, $options){
        $sql = "INSERT INTO Poll (groupId, question, endTime)
		VALUES ($group_id, $_question, $end_time)";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            //get id
            $poll_id = $this->conn->lastInsertId();
            $optionToPoll = new OptionToPoll($this->conn);
            $optionToPoll->addOptionsToPoll($group_id, $poll_id, $options);
            echo "poll added";
        } catch (Exception $e) {
            echo "error" . $e->getMessage();
        }
    }

    public function getPollsForUser($user_id){
        $sql = "SELECT tempo.pollId, tempo.question, UserInfo.name, tempo.groupName, tempo.startTime, tempo.endTime FROM 
             (SELECT temp.pollId, temp.question, temp.startTime, temp.endTime, GroupInfo.name as groupName, GroupInfo.owner FROM 
                  (SELECT id as pollId, groupId, question, startTime, endTime FROM `Poll` WHERE groupId IN 
                        (SELECT groupId FROM `UserToGroup` WHERE userId = $user_id))
                  as temp
             INNER JOIN GroupInfo on GroupInfo.id = temp.groupId) 
             as tempo 
        INNER JOIN UserInfo on UserInfo.id = tempo.owner";

        $array_all_data = array();
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $num = $stmt->rowCount();

            $array_all_data['status'] = 1;
            $array_all_data['completed'] = array();
            $array_all_data['active'] = array();
            if ($num > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $row_item=array(
                        "id" => $pollId,
                        "title" => $question,
                        "author" => $name,
                        "tag" => $groupName,
                        "time_created" => $startTime,
                        "time_ended" => $endTime
                    );

                    //check time, if it has ended and add to active or completed
                    if(true){
                        array_push($array_all_data['active'], $row_item);
                    }else{
                        array_push($array_all_data['completed'], $row_item);
                    }
                }
            }
        } catch(Exception $e){
            $array_all_data['status'] = 0;
            $array_all_data['msg'] = 'error, polls cannot be retrieved';
            //echo "error" . $e->getMessage();
        }

        return $array_all_data;
    }


}

?>
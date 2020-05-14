<?php
header("Content-type:application/json");

$group_id = isset($_POST['group_id']) ? $_POST['group_id'] : die();
$question = isset($_POST['question']) ? $_POST['question'] : die();
$end_time = isset($_POST['end_time']) ? $_POST['end_time'] : die();
$options = isset($_POST['options']) ? $_POST['options'] : die();

//include
include_once '../util/objects/config/Database.php';


//connection to db
$db = new Database();
$conn = $db->getConnection();

//query to add poll
$sql = "INSERT INTO Poll (groupId, question, endTime)
		VALUES ($group_id, $question, $end_time)";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    //get id
    $poll_id = $conn->lastInsertId();

    addOptions($group_id, (int)$poll_id, $options, $conn);


    $return_msg = array("status" => 1,
					   "poll_id" => (int)$poll_id);
	echo json_encode($return_msg);
	
} catch (Exception $e) {
   $return_msg = array("status" => 0,
					   "msg" => "poll not added");
	echo json_encode($return_msg);
}

$db->closeConnection();


function addOptions($group_id, $poll_id, $options, $conn){
    $j = 1;
    foreach($options as &$value){
       $sql = "INSERT INTO OptionToPoll (optionId, pollId, _option)
					VALUES ($j, $poll_id, $value)";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $j = $j + 1;
        }
}

?>
<?php

header("Content-type:application/json");

$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : die("user_id is missing");

//include
include_once '../util/objects/config/Database.php';


//connection to db
$db = new Database();
$conn = $db->getConnection();


$sql = "SELECT GroupInfo.name, temp.* FROM GroupInfo
		INNER JOIN (
				SELECT * from Poll 
				WHERE groupId IN
				(SELECT id FROM GroupInfo 
				WHERE owner = $user_id)
				) as temp
		ON temp.groupId = GroupInfo.id";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute();
	$num = $stmt->rowCount();

    $all_data = array();
    $all_data['status'] = 1;
    $all_data['active_polls'] = array();
    $all_data['completed_polls'] = array();

    if($num != 0){
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
			
            
			$row_item = array(
					"poll_id" => (int)$id,
					"group_id" => $groupId,
					"tag" => $name,
					"title" => $question,
					"time_created" => $startTime,
					"time_ended" => $endTime
			 );
				
			$end_time = strtotime($endTime);
			$curtime = time();			
			if($end_time - $curtime > 0){
				array_push($all_data['active_polls'], $row_item);
			}else{
				array_push($all_data['completed_polls'], $row_item);
			}
		}
    }

   echo json_encode($all_data);
} catch (Exception $e) {
	$return_msg = array("status" => 0, "msg" => "super user polls could not be retrieved");
    echo json_encode($return_msg);
}


$db->closeConnection();

?>
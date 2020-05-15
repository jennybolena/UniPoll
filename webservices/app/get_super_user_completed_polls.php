<?php

header("Content-type:application/json");

$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : die();

//include
include_once '../util/objects/config/Database.php';


//connection to db
$db = new Database();
$conn = $db->getConnection();

$dd = date(format,timestamp);
$sql = "SELECT * from Poll 
		WHERE groupId IN
		(SELECT id FROM GroupInfo 
			WHERE owner = $user_id)";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute();
	$num = $stmt->rowCount();

    $all_data = array();
    $all_data['status'] = 1;
    $all_data['data'] = array();

    if($num != 0){
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
			$end_time = strtotime($endTime);
			$curtime = time();
            if($end_time - $curtime <= 0){
				$row_item = array(
				"poll_id" => (int)$id,
				"group_id" => $groupId,
				"question" => $question,
				"start_time" => $startTime,
				"end_time" => $endTime
			     );
				array_push($all_data['data'], $row_item);
			}
		}
    }

   echo json_encode($all_data);
} catch (Exception $e) {
	$return_msg = array("status" => 0, "msg" => "super user active polls could bot be retrieved");
    echo json_encode($return_msg);
}


$db->closeConnection();

?>
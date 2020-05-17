<?php
header("Content-type:application/json");

$group_id = isset($_POST['group_id']) ? $_POST['group_id'] : die("group_id is missing");
$question = isset($_POST['question']) ? $_POST['question'] : die("question is missing");
$end_time = isset($_POST['end_time']) ? $_POST['end_time'] : die("end_time is missing");
$options = isset($_POST['options']) ? $_POST['options'] : die("option is missing");

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
					   
	//get all user's tokens (who are enrolled in this group)
	$sql = "";
	$stmt = $conn->prepare($sql);
    $stmt->execute();
	$num = $stmt->rowCount();


    if($num != 0){
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
			sendPushNotification($token, $message);
		}
	}
			
					   
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

function sendPushNotification($token, $message){
	$apiKey = '';
	$fields = array('to' => $token, 'notification' => $message);
	$headers = array('Authorization: key=' .apiKey, 'Content-Type: application/json');
	$url = '';
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	
	curl_exec($ch);
	curl_close($ch);
}

?>
<?php
header("Content-type:application/json");

$group_id = isset($_POST['group_id']) ? $_POST['group_id'] : die("group_id is missing");
$question = isset($_POST['question']) ? $_POST['question'] : die("question is missing");
$end_time = isset($_POST['end_time']) ? $_POST['end_time'] : die("end_time is missing");
$options_ = isset($_POST['options']) ? $_POST['options'] : die("option is missing");

echo "1";
$options = json_decode($options_);

echo "2";

//include
include_once '../util/objects/config/Database.php';

//connection to db
$db = new Database();
$conn = $db->getConnection();

//query to add poll
$sql = "INSERT INTO Poll (groupId, question, endTime)
		VALUES (:group_id, :question, :end_time)";

try {
    $stmt = $conn->prepare($sql);
	$stmt->bindParam(':user_id', $user_id);
	$stmt->bindParam(':question', $question);
	$stmt->bindParam(':end_time', $end_time);
    $stmt->execute();

    //get id
    $poll_id = $conn->lastInsertId();

    addOptions($group_id, (int)$poll_id, $options, $conn);


    $return_msg = array("status" => 1,
					   "poll_id" => (int)$poll_id);
					   
	//get all user's tokens (who are enrolled in this group)
	$sql = "SELECT token FROM PushNotification WHERE userId in (SELECT userId from UserToGroup WHERE groupId = :group_id)";
	$stmt = $conn->prepare($sql);
	$stmt->bindParam(':group_id', $group_id);
    $stmt->execute();
	$num = $stmt->rowCount();


   /* if($num != 0){
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
			sendPushNotification($token, $question, $poll_id);
		}
	}*/
			
					   
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
					VALUES (:j, :poll_id, :value)";

            $stmt = $conn->prepare($sql);
			$stmt->bindParam(':poll_id', $poll_id);
			$stmt->bindParam(':j', $j);
			$stmt->bindParam(':value', $value);
            $stmt->execute();
            $j = $j + 1;
        }
}

/*function sendPushNotification($token, $question, $poll_id){
	$GOOGLE_API_KEY = "AAAAeKmKJlw:APA91bHeqYgQcT0jFaFMgz6OBU57g9hDknwe8iRY-Oojn62tJuYlJS6_zGCCovnKzws-dQVMzyr5a79frnTNI2WlP2x1PHqPpBL_PF04D4DaH1mFtz1ZwEEQLnKU27-zWAMvECTpoYb5";
	$GOOGLE_GCM_URL = "https://fcm.googleapis.com/fcm/send";

	$fields = array(
		'to'       => $token,
		'sound'    => 'default',
		'priority' => 'high',
		'data'	   => array(
			"title"          => $question,
			"new_poll_id" => $poll_id
		)
	);

	$headers = array(
		$GOOGLE_GCM_URL,
		'Content-Type: application/json',
		'Authorization: key='.$GOOGLE_API_KEY
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $GOOGLE_GCM_URL);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	$result = curl_exec($ch);
	curl_close($ch);
}*/

?>

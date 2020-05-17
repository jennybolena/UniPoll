<?php
header("Content-type:application/json");

// Get student email
$email = isset($_POST['email']) ? $_POST['email'] : die();

// Get mobile push notification token
$token = isset($_POST['token']) ? $_POST['token'] : die();

$user_id = hashMail($email);


//include
include_once '../util/objects/config/Database.php';


//connection to db
$db = new Database();
$conn = $db->getConnection();


try{
	
	$sql = "SELECT * FROM UserInfo WHERE id = :user_id";
	$stmt = $conn->prepare($sql);
	$stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
	$num = $stmt->rowCount();
	
	if($num == 0){
		//add user to db
		$sql = "INSERT INTO UserInfo (id) VALUES (:user_id)";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':user_id', $user_id);
		$stmt->execute();
		
		//add automatically to groups AUEB and UNIPOLL
		$sql = "INSERT INTO UserToGroup (userId, groupId)
							VALUEs(:user_id, '1'),
								  (:user_id, '2') ";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':user_id', $user_id);
		$stmt->execute();
		
		//add token
		$sql = "INSERT INTO PushNotification (userId, token)
											VALUES (:user_id, $token)";
		$stmt = $conn->prepare($sql);
	    $stmt->bindParam(':user_id', $user_id);
	    //$stmt->bindParam(':device_token', $token);
		$stmt->execute();
	}else{
		//user alredy exitsts, add token if user has logged from a new device
		$sql = "SELECT * FROM PushNotification WHERE token = $token";
		$stmt = $conn->prepare($sql);
    	$stmt->execute();
		$num = $stmt->rowCount();
		
		if($num == 0){
			$sql = "INSERT INTO PushNotification (userId, token)
											VALUES (:user_id, $token)";
			$stmt = $conn->prepare($sql);
	    	$stmt->bindParam(':user_id', $user_id);
			$stmt->execute();
		}
	}
	
	
	
	
	
	
	echo "done";
}catch(Exception $e){
	return $return_msg = array("status" => 0,
						        "msg" => "cannnot give you user details"
				               );
	echo json_encode($return_msg);	
}


function hashMail($email){
	
	return 'sss';
}
?>
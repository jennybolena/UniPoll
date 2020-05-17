<?php
header("Content-type:application/json");

// Get student email
$email = isset($_POST['email']) ? $_POST['email'] : die();

// Get mobile push notification token
$token = isset($_POST['token']) ? $_POST['token'] : die();


//include
include_once '../util/objects/config/Database.php';


//connection to db
$db = new Database();
$conn = $db->getConnection();


$user_id = hashMail($email);
try{
	//check if user already exists
	$sql = "SELECT * FROM UserInfo WHERE id = $user_id";
	$stmt = $conn->prepare($sql);
    $stmt->execute();
    $num = $stmt->rowCount();

    if($num == 0){
		//add user to db
		$sql = "INSERT INTO UserInfo (id) VALUES ($user_id)";
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		
		//add automatically to groups AUEB and UNIPOLL
		$sql = "INSERT INTO UserToGroup (userId, groupId)
							VALUEs($user_id, '1'),
								  ($user_id, '2') ";
		$stmt = $conn->prepare($sql);
		$stmt->execute();
	}
	
	//add token if user has logged from a new device
	
	echo "done";
}catch(Exception $e){
	return $msg = array("status" => 0,
						"msg" => "cannnot give you user details");
	
	
}


public function hashMail($email){
	
	return 'sds';
}
?>
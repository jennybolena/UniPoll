<?php
/**
 * Use this webservice to get e-class token.
 */

header("Content-type:application/json");

$username = isset($_POST['username']) ? $_POST['username'] : die('username is missing');
$password = isset($_POST['password']) ? $_POST['password'] : die('password is missing');
$token = isset($_POST['token']) ? $_POST['token'] : die('token is missing');

// Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/

//get login token
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://eclass.aueb.gr/modules/mobile/mlogin.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "uname=$username&pass=$password");

$headers = array();
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
	$return_msg = array("status" => 0, "msg" => "error");
	echo json_encode($return_msg);
	die();
}

if($result == "FAILED"){
	$return_msg = array("status" => 0, "msg" => "username or password is incorrect");
	echo json_encode($return_msg);
	die();
}

$eclass_token = $result;
if(strpos($result, ">") == true){
    $eclass_token = substr($result, strrpos($result, '>') + 1);
}

curl_close($ch);


//get courses
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://eclass.aueb.gr/modules/mobile/mcourses.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "token=$eclass_token");

$headers = array();
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$courses = curl_exec($ch);
if (curl_errno($ch)) {
    $return_msg = array("status" => 0, "msg" => "error");
	echo json_encode($return_msg);
	die();
}
curl_close($ch);



//include
include_once '../util/objects/config/Database.php';


//connection to db
$db = new Database();
$conn = $db->getConnection();

//check if user already exists
$email = $username . "@aueb.gr";
$user_id = hashMail($email);


try{
	$userExists = userAlreadyExist($user_id, $conn);
	if($userExists == 0){
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
	}

	addToken($user_id, $token, $conn);

	//add eclass groups
	addEclassGroups($courses, $conn);
	//add user to groups
	addUserToEclassGroups($user_id, $courses, $conn);

	//check if user is super user
	$sql = "SELECT isSuperUser FROM UserInfo WHERE id = :user_id";
	$stmt = $conn->prepare($sql);
	$stmt->bindParam(':user_id', $user_id);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	extract($row);

	$return_msg = array("status" => 1, "user_id" => $user_id, "is_super_user" => (int)$isSuperUser);
	echo json_encode($return_msg);
	
	
}catch(Exception $e){
	$return_msg = array("status" => 0, "msg" => "error");
	echo json_encode($return_msg);
}


$db->closeConnection();


function userAlreadyExist($user_id, $conn){
    $sql = "SELECT * FROM UserInfo WHERE id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $num = $stmt->rowCount();
	return $num;
	
}

function hashMail($email){
    return sha1($email);
}

function addToken($user_id, $token, $conn){
    $sql = "INSERT IGNORE INTO PushNotification (userId, token)	
											VALUES (:user_id, :token)";
    $stmt = $conn->prepare($sql);
	$stmt->bindParam(':token', $token);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
}


function addEclassGroups($all_courses, $conn){
	
	
	$courses = new SimpleXMLElement($all_courses);
	
	foreach ($courses->coursegroup[0]->course as $course) {
		$course_id = $course['code'];
		$course_title = $course['title'];
		$course_description = $course['description'];	
	    $sql = "INSERT IGNORE INTO GroupInfo (id, name, description)
										VALUES (:course_id, :course_title, :course_description)";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':course_id', $course_id);
		$stmt->bindParam(':course_title', $course_title);
		$stmt->bindParam(':course_description', $course_description);
		$stmt->execute();
	}	
	
}

function addUserToEclassGroups($user_id, $all_courses, $conn){
	
	$courses = new SimpleXMLElement($all_courses);
	
	foreach ($courses->coursegroup[0]->course as $course) {
		$course_id = $course['code'];
		$sql = "INSERT IGNORE INTO UserToGroup (userId, groupId)
									VALUES (:user_id, :course_id)";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':course_id', $course_id);
		$stmt->bindParam(':user_id', $user_id);
		$stmt->execute();
	}
}
?>


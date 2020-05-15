<?php
header("Content-type:application/json");

$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : die();

//include
include_once '../util/objects/config/Database.php';


//connection to db
$db = new Database();
$conn = $db->getConnection();

$sql = "SELECT tempo.*, UserInfo.name as author FROM UserInfo
INNER JOIN 
(SELECT GroupInfo.name as groupName, GroupInfo.owner, temp.* FROM GroupInfo 
INNER JOIN (SELECT * from Poll WHERE groupId 
IN
(SELECT groupId FROM UserToGroup 
WHERE userId = 'xxx')) as temp
ON temp.groupId = GroupInfo.id) as tempo
ON tempo.owner = UserInfo.id
";

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
					"id" => (int)$id,
					"author" => $author,	
					"tag" => $groupName,
					"title" => $question,
					"time_created" => $startTime,
					"time_ended" => $endTime
				);
				
				array_push($all_data['data'], $row_item);
			}			
		}
    }

   echo json_encode($all_data);
} catch (Exception $e) {
	$return_msg = array("status" => 0, "msg" => "user completed polls could not be retrieved");
    echo json_encode($return_msg);
}


$db->closeConnection();

?>
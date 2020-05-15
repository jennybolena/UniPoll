<?php
header("Content-type:application/json");

$poll_id = isset($_POST['poll_id']) ? $_POST['poll_id'] : die();

$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : die();

//connect to db
include_once '../util/objects/config/Database.php';
$db = new Database();
$conn = $db->getConnection();


//see if user has voted
$sql = "SELECT optionId from Vote WHERE pollId = $poll_id AND userId = $user_id"; 


try {
    $all_data = array();
    $all_data['status'] = 1;
    $all_data['poll_id'] = (int)$poll_id;
    $all_data['question'];
    $all_data['author'];
    $all_data['poll_stats'] = array();
    $all_data['user_vote_id'] = 0;
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $num = $stmt->rowCount();
    if($num == 1){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        $all_data['user_vote_id'] = (int)$optionId;
    }
	
	
	 $sql = "SELECT optionId, _option, COUNT(voteOptionId) as totalVotes FROM 
			(SELECT OptionToPoll.optionId, OptionToPoll._option, Vote.optionId as voteOptionId FROM OptionToPoll 
			 LEFT JOIN Vote on (OptionToPoll.pollId = Vote.pollId AND OptionToPoll.optionId = Vote.optionId) 
			 WHERE OptionToPoll.pollId = $poll_id) as temp
			 GROUP By (optionId)";
	 $stmt = $conn->prepare($sql);
     $stmt->execute();
     $num = $stmt->rowCount();
     if ($num > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			extract($row);
			$row_item = array(
				"option_id" => (int)$optionId,
				"option" => $_option,
				"votes" => (int)$totalVotes
			);
			array_push($all_data['poll_stats'], $row_item);
        }	
    }
	
	//get poll question
	$sql = "SELECT question FROM Poll WHERE id = $poll_id"; 
	$stmt = $conn->prepare($sql);
    $stmt->execute();
	
	 if ($num == 1) {
		$all_data['question'] = $question;
	 }
	 
	 //get poll author
	 $sql = "SELECT UserInfo.name as author FROM UserInfo 
			INNER JOIN 
			(SELECT owner from GroupInfo INNER JOIN
			(select groupId from Poll Where id = $poll_id) as temp
			ON temp.groupId = GroupInfo.id) as tempo
			ON UserInfo.id = tempo.owner"; 
	 $stmt = $conn->prepare($sql);
     $stmt->execute();
	
	 if ($num == 1) {
		$all_data['author'] = $author;
	 }
	
	echo json_encode($all_data);
} catch(Exception $e){
    $return_msg = array("status" => 0, "msg" => "poll details cannot be retrieved");
    echo json_encode($return_msg);
}
$db->closeConnection();

?>

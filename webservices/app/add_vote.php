<?php
header("Content-type:application/json");

// Get student id
$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : die("user_id is missing");

// Get option id
$option_id = isset($_POST['option_id']) ? $_POST['option_id'] : die("option_id is missing");

// Get poll id
$poll_id = isset($_POST['poll_id']) ? $_POST['poll_id'] : die("poll_id ");

//include
include_once '../util/objects/Vote.php';
include_once '../util/objects/config/Database.php';


//connection to db
$db = new Database();
$conn = $db->getConnection();

//check if user has already voted for this poll
$sql = "SELECT * FROM Vote WHERE userId = :user_id AND pollId = :poll_id";

$return_msg;
try{
    $stmt = $conn->prepare($sql);
	$stmt->bindParam(':user_id', $user_id);
	$stmt->bindParam(':poll_id', (int)$poll_id);
    $stmt->execute();
    $num = $stmt->rowCount();

    $vote = new Vote($conn, $poll_id, $option_id, $user_id);
    /*if($num == 1){
        $vote->updateVote();
    }else{
        $vote->addVote();
    }
    */
    $return_msg=array(
        "status" => 1,
        "msg" => "user voted"
    );
} catch(Exception $e){
    $return_msg=array(
        "status" => 0,
        "msg" => "user voted"
    );
}

echo json_encode($return_msg);

$db->closeConnection();

?>
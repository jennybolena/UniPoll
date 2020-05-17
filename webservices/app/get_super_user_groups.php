<?php
header("Content-type:application/json");

$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : die("user_id is missing");

//include
include_once '../util/objects/config/Database.php';


//connection to db
$db = new Database();
$conn = $db->getConnection();

$sql = "SELECT id, name FROM GroupInfo WHERE owner = $user_id";
try{
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $num = $stmt->rowCount();

    $all_data = array();
    $all_data['status'] = 1;
    $all_data['data'] = array();

    if($num != 0){
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $row_item = array(
                "group_id" => $id,
                "group_name" => $name
            );
            //echo $row_item;
            array_push($all_data['data'], $row_item);
        }
    }
    echo json_encode($all_data);
}catch (Exception $e){
    $return_msg = array("status" => 0, "msg" => "usr groups cannot be retrieved");
    echo json_encode($return_msg);
}

?>
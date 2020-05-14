<?php

// Include necessary files
include_once "../util/objects/config/Database.php";

// Get student email
$email = isset($_POST['email']) ? $_POST['email'] : die();

// Get mobile push notification token
$email = isset($_POST['token']) ? $_POST['token'] : die();

// Status variable used for knowing if everything was ok
$is_process_successful = true;

// Store student in database
$database = new Database();
$connection = $database->getConnection();
$query = "write your query here"; //todo write your query here
$stmt = $connection->prepare($query);
if($stmt->execute()){
    //todo write what happens when query is executed succesfully
}else{
    //todo write what happens when query fails
    $is_process_successful = false;
}

// Store mobile push notification token in database
$database = new Database();
$connection = $database->getConnection();
$query = "write your query here"; //todo write your query here
$stmt = $connection->prepare($query);
if($stmt->execute()){
    //todo write what happens when query is executed succesfully
}else{
    //todo write what happens when query fails
    $is_process_successful = false;
}

// Return json to let mobile device know if everything was successful
if ($is_process_successful){
    echo json_encode(
        ["status" => "1"]
    );
}else{
    echo json_encode(
        ["status" => "0",
         "error_msg" => "Server could not store given data"]
    );
}
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Used to send push notification for a new poll id
 */
$fcm_token = isset($_POST['fcm_token']) ? $_POST['fcm_token'] : die("fcm_token is missing");
$notification_title = isset($_POST['notification_title']) ? $_POST['notification_title'] : die("notification_title is missing");
$notification_description = isset($_POST['notification_description']) ? $_POST['notification_description'] : die("notification_description is missing");
$new_poll_id = isset($_POST['new_poll_id']) ? $_POST['new_poll_id'] : die("new_poll_id is missing");

$GOOGLE_API_KEY = "AAAAeKmKJlw:APA91bHeqYgQcT0jFaFMgz6OBU57g9hDknwe8iRY-Oojn62tJuYlJS6_zGCCovnKzws-dQVMzyr5a79frnTNI2WlP2x1PHqPpBL_PF04D4DaH1mFtz1ZwEEQLnKU27-zWAMvECTpoYb5";
$GOOGLE_GCM_URL = "https://fcm.googleapis.com/fcm/send";

$fields = array(
    'to'       => $fcm_token,
    'sound'    => 'default',
    'priority' => 'high',
    'data'	   => array(
        "title"          => $notification_title,
        "description" 	 => $notification_description,
        "new_poll_id" => $new_poll_id
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

if ($result === FALSE)
    return 'Problem occurred: ' . curl_error($ch);

echo $result;

<?php

if ($_POST['token'] != /* ENTER TOKEN HERE */) {
  header('HTTP/1.0 401 Unauthorized');
  print "Sorry, your credentials are not valid";

} else {

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    include_once '../config/Database.php';
    include_once '../models/PhoneNumber.php';

    $num = $_POST['num'];
    $num = preg_replace('/[^0-9]/', '', $num);
    $num = substr($num, 0, 10);


    $database = new Database();
    $db = $database->connect();

    $post = new PhoneNumber($db);
    $result = $post->read($num);
    $num = $result->rowCount();

    if($num > 0){
    $posts_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $post_item = array(
            'UserID' => $UserID,
            'LastName' => $LastName,
            'FirstName' => $FirstName,
            'dob' => $dob,
            'ContactData' => $ContactData

            );
            array_push($posts_arr, $post_item);
        }

        echo json_encode($posts_arr);

    }
}

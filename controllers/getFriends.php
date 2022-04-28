<?php
require_once "../controllers/init.php";
header("Content-Type: text/plain");

$friends = $usersRepo->getAllStatusCode("A", $_SESSION['username']);

$toReturn = '{ "friends": [';
foreach ($friends as $friend){
    $toReturn .= $friend->toJson();
    if($friend != end($friends)){
        $toReturn .= ", ";
    }
}
$toReturn .= "]}";
echo $toReturn;
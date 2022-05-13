<?php
if(!isset($usersRepo)){
    require_once "init.php";
}
header("Content-Type: text/plain");

$friends = $usersRepo->getAllStatusCode("A", $_SESSION['username']);

//turns the data (objects) into JSON format and puts them all in a JSON array called friends
$toReturn = '{ "friends": [';
foreach ($friends as $friend){
    $toReturn .= $friend->toJson();
    //the last item in the JSON array should not have a comma otherwise it won't work
    if($friend != end($friends)){
        $toReturn .= ", ";
    }
}
$toReturn .= "]}";
echo $toReturn;
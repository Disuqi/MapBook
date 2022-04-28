<?php
require_once "init.php";
header("Content-Type: text/plain");

$users = $usersRepo->getPageOfUsers($_GET['page']);
$loggedIn = isset($_SESSION['loggedIn']);
$toReturn = '{ "users": [';
foreach ($users as $user){
    if($loggedIn){
        $friendship = $friendshipRepo->areFriends(["requesterId" => $_SESSION['username'], "addresseeId" => $user->getUsername()]);
        $user->setFriendship($friendship);
    }
    $toReturn .= $user->toJson();
    if($user != end($users)){
        $toReturn .= ", ";
    }
}
$toReturn .= "]}";

echo $toReturn;
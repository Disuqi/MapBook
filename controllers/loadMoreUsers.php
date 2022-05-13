<?php
header("Content-Type: text/plain");
if (!isset($usersRepo)) {
    require_once "init.php";
}
if(!isset($_GET['token']) || $_GET['token'] != $_SESSION['token'] ){
    echo 'Page not found';
}else{
    //get the user objets from database
    $users = $usersRepo->getPageOfUsers($_GET['page']);
    $loggedIn = isset($_SESSION['loggedIn']);


    //turn into  a JSON array called users
    $toReturn = '{ "users": [';
    foreach ($users as $user) {
        //check the friendship status for every user with the loggedIn user
        if ($loggedIn) {
            $friendship = $friendshipRepo->areFriends(["requesterId" => $_SESSION['username'], "addresseeId" => $user->getUsername()]);
            $user->setFriendship($friendship);
        }
        $toReturn .= $user->toJson();
        if ($user != end($users)) {
            $toReturn .= ", ";
        }
    }
    $toReturn .= "]}";

    echo $toReturn;
}
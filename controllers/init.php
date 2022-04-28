<?php
require_once "../models/UsersRepo.php";
require_once "../models/FriendshipRepo.php";
require_once "../models/ImagesRepo.php";
require_once "../models/Signer.php";
require_once "../models/Images.php";
require_once "../models/Checker.php";

if(session_id() == ""){
    session_start();
}

//Repos
$usersRepo = new UsersRepo();
$imagesRepo = new ImagesRepo();
$friendshipRepo = new FriendshipRepo();

//Others
$signer = new Signer();
$imageHandler = new Images();
$check = new Checker();

//View
$view = new stdClass();

$view->pageTitle = 'MyFriends';

function generateLink($action, $requester, $addressee){
    $base = "index.php?";
    if(isset($_GET['search'])){
        $base.= "search=".$_GET['search']."&";
    }
    return $base . "friends=$action&requester=$requester&addressee=$addressee";
}
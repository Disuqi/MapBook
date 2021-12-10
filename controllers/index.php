<?php

//checking if a session has already been started, if not then start one
if(session_id() == ''){
    session_start();
}

//making an empty class that will be used in the view
$view = new stdClass();
$view->pageTitle = "Home";
$view->usersList = "";
//required later in the code
require_once('../models/Signer.php');
require_once('cookie.php');
require_once('../models/UserLister.php');

$userLister = new UserLister();
if(isset($_GET['friends']) && isset($_SESSION['loggedIn']) && (strcasecmp($_GET['requester'], $_SESSION['username']) == 0 || strcasecmp($_GET['addressee'], $_SESSION['username']) == 0)) {
        require_once('friendship.php');
}

if(isset($_GET['search'])){
    switch($_GET['search']){
        case 'friends':
            $view->usersList = $userLister->getFriends($_SESSION['username']);
            break;
        case '':
            $view->usersList = $userLister->getAllUsers();
            break;
        default:
            $view->usersList = $userLister->search($_GET['search']);
            break;

    }
}else {
    $view->usersList = $userLister->getAllUsers();
}

require_once("header.php");
require_once("../views/index.phtml");
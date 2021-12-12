<?php

//checking if a session has already been started, if not then start one
ini_set('max_execution_time', 0);
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
    if(isset($_SESSION['loggedIn']))
    {
        switch($_GET['search']){
            case 'friends':
                $view->title = '<h1 style="margin-top: 80px; margin-left: 20px">Friend List</h1>';
                $users = $userLister->getFriends($_SESSION['username']);
                $view->usersList = $users == null? "<i class='m-2 bi bi-emoji-frown' style='font-size: 20px'> No Friends</i>" :  $users;
                break;
            case 'requests':
                $view->title = '<h1 style="margin-top: 80px; margin-left: 20px">Requests</h1>';
                $users = $userLister->getAllRequests($_SESSION['username']);
                $view->usersList = $users == null? "<i class='m-2 bi bi-inbox-fill' style='font-size: 20px'> No Pending Requests</i>" :  $users;
                break;
            case 'declined':
                $view->title = '<h1 style="margin-top: 80px; margin-left: 20px">Declined</h1>';
                $users= $userLister->getAllDeclined($_SESSION['username']);
                $view->usersList = $users == null? "<i class='m-2 bi bi-inbox-fill' style='font-size: 20px'> No Declined Requests</i>" :  $users;
                break;
            case '':
                $view->title = '<h1 style="margin-top: 80px; margin-left: 20px">All Users</h1>';
                $users = $userLister->getAllUsers();
                $view->usersList = $users == null ? "<i class='m-2 bi bi-inbox-fill' style='font-size: 20px'> No Users</i>" : $users;
                break;
            default:
                $view->title = '<h1 style="margin-top: 80px; margin-left: 20px">Search Result</h1>';
                $view->usersList = $userLister->search($_GET['search']);
                break;
    }}else{
        $view->title = '<h1 style="margin-top: 80px; margin-left: 20px">Search Result</h1>';
        $view->usersList = $userLister->anonymousSearch($_GET['search']);
    }
}else {
    $view->title = '<h1 style="margin-top: 80px; margin-left: 20px">All Users</h1>';
    //$view->usersList = $userLister->getAllUsers();
}

require_once("header.php");
require_once("../views/index.phtml");
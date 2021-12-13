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
require_once ('pagination.php');
if(isset($_GET['friends']) && isset($_SESSION['loggedIn']) && (strcasecmp($_GET['requester'], $_SESSION['username']) == 0 || strcasecmp($_GET['addressee'], $_SESSION['username']) == 0)) {
        require_once('friendship.php');
}

if(isset($_GET['search'])){
    if(isset($_SESSION['loggedIn']))
    {
        switch($_GET['search']){
            case 'friends':
                $users = $userLister->getFriends($_SESSION['username']);
                $view->usersList = $users == null? "<div style='margin-top: 80px; margin-left: 20px'><i class='m-2 bi bi-emoji-frown' style='font-size: 25px'> No Friends</i></div>" :  $users;
                break;
            case 'requests':
                $users = $userLister->getAllRequests($_SESSION['username']);
                $view->usersList = $users == null? "<div style='margin-top: 80px; margin-left: 20px'><i class='m-2 bi bi-inbox-fill' style='font-size: 25px'> No Pending Requests</i></div>" :  $users;
                break;
            case 'declined':
                $users= $userLister->getAllDeclined($_SESSION['username']);
                $view->usersList = $users == null? "<div style='margin-top: 80px; margin-left: 20px'><i class='m-2 bi bi-inbox-fill' style='font-size: 25px'> No Declined Requests</i></div>" :  $users;
                break;
            case '':
                $view->usersList .= '<h1 style="margin-top: 80px; margin-left: 20px">All Users</h1>';
                $users = $userLister->getAllUsers();
                $view->usersList = $users == null ? "<div style='margin-top: 40px; margin-left: 20px'><i class='m-2 bi bi-inbox-fill' style='font-size: 25px'> No Users</i></div>" : $users;
                break;
            default:
                $view->usersList .= $userLister->search($_GET['search']);
                break;
    }}else{
        $view->usersList .= $userLister->anonymousSearch($_GET['search']);
    }
}else {
    $view->usersList .= '<h1 style="margin-top: 80px; margin-left: 20px">All Users</h1>';
    $view->usersList .= $userLister->getPageOfUsers($currentPage);
}

require_once("header.php");
require_once("../views/index.phtml");
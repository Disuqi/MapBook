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
//if a user is logged in and a request including him is being made
if(isset($_GET['friends']) && isset($_SESSION['loggedIn']) && (strcasecmp($_GET['requester'], $_SESSION['username']) == 0 || strcasecmp($_GET['addressee'], $_SESSION['username']) == 0)) {
    //make friend request
    require_once('friendship.php');
}
require_once("search.php");
require_once("header.php");
require_once("../views/index.phtml");
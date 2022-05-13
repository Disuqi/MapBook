<?php
//initializing necessary things
require_once "init.php";
require_once'cookie.php';
//generating random string to be used as token
$_SESSION['token'] =  substr(str_shuffle(MD5(microtime())), 0, 20);
//if a user is logged in and a request including him is being made
if(isset($_GET['friends']) && isset($_SESSION['loggedIn']) && (strcasecmp($_GET['requester'], $_SESSION['username']) == 0 || strcasecmp($_GET['addressee'], $_SESSION['username']) == 0)) {
    //make friend request
    require_once('friendship.php');
}

require_once("users.php");
require_once("header.php");
require_once("../views/index.phtml");
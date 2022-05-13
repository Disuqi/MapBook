<?php
require_once "init.php";
require_once'cookie.php';
if(!isset($_SESSION["loggedIn"]) || !$_SESSION["loggedIn"]){
    header("Location: http://" . $_SERVER['HTTP_HOST'] . "/controllers/index.php");
    exit;
}

$view->pageTitle = "Map";
require_once "header.php";
require_once "../views/map.phtml";
<?php
if(!isset($usersRepo)){
    require_once "init.php";
}

//simply update the database with the new info
$usersRepo->updateAttribute("lat", $_GET['lat'], $_SESSION['username']);
$usersRepo->updateAttribute("lng", $_GET['lng'], $_SESSION['username']);
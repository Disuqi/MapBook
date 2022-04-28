<?php
require_once "init.php";

$usersRepo->updateAttribute("lat", $_GET['lat'], $_SESSION['username']);
$usersRepo->updateAttribute("lng", $_GET['lng'], $_SESSION['username']);
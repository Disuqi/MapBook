<?php
if(!isset($_SESSION)) {
    session_start();
}
$_SESSION['loggedIn'] = false;
$_SESSION['username'] = null;
$_SESSION['profileImage'] = null;
require_once("index.php");
<?php
session_start();
$_SESSION['loggedIn'] = false;
require_once("index.php");
<?php
require_once("../models/Checker.php");
$view = new stdClass();
$view->pageTitle = "Sign Up";
if(isset($_POST['submit'])){
     if(Checker::checkUsername($_POST['username'])){
         echo "found it";
     }
//    $uploaddir = "../images/" . $_POST['username'] . '/';
//    $uploadfile = $uploaddir . $_POST['username'] . 1 . '.png';
//    //if dir doesnt exist make it
//    move_uploaded_file($_FILES['profilePic']['tmp_name'], $uploadfile);
}
require_once("../views/signUp.phtml");
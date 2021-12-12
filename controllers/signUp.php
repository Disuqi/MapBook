<?php
require_once("../models/Checker.php");
require_once("../models/Images.php");
$view = new stdClass();
$view->pageTitle = "Sign Up";
$view->validation = null;
if(isset($_POST['submit'])){
    $check = new Checker();
    $_POST['firstName'] = strtolower($_POST['firstName']);
    $_POST['firstName'] = ucfirst($_POST['firstName']);
    $_POST['lastName'] = strtolower($_POST['lastName']);
    $_POST['lastName'] = ucfirst($_POST['lastName']);
    switch($check->checkSingUp($_POST)){
        case "IU":
            $view->validation = "Invalid Username";
            break;
        case "EU":
            $view->validation = "The Username is already in use";
            break;
        case "IE":
            $view->validation = "Invalid Email";
            break;
        case "EE":
            $view->validation = "The email is already in use";
            break;
        case "IN":
            $view->validation = "Invalid Name";
            break;
        case "IF":
            $view->validation = "Invalid First Name";
            break;
        case "IL":
            $view->validation = "Invalid Last Name";
            break;
        case "NP":
            $view->validation = "No password inserted";
            break;
        case "SP":
            $view->validation = "The password is too short";
            break;
        case "EP":
            $view->validation = "The password is too simple<br>Try adding special characters";
            break;
        case "NM":
            $view->validation = "The passwords do not match";
            break;
        case 'OK':
            require_once("../models/Signer.php");
            $signer = new Signer();
            $_POST['lat'] = 0.0;
            $_POST['lng'] = 0.0;
            $view->validation = $signer->signUp($_POST);
            break;
    }
}
require_once("../views/signUp.phtml");
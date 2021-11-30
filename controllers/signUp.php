<?php
require_once("../models/Checker.php");
require_once("../models/Images.php");
$view = new stdClass();
$view->pageTitle = "Sign Up";
$view->validation = null;
if(isset($_POST['submit'])){
    $check = new Checker();
    $imageHandler = new Images();
    $repo = new UsersRepo();
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
            $un = $_POST['username']; //un is an abbreviation for username
            $profileImage = $imageHandler->addProfileImage();
            if($_FILES['profileImage']['name'] != ""){
                    $_POST['profileImage'] = $un . '_' . 1 . '.png';
                    $_SESSION['profileImage'] = "../images/" . $un . '/' . $_POST['profileImage'];
                    if(!$profileImage) {
                        $view->validation = "There is something wrong with your image";
                        break;
                    }
            }else{
                $_SESSION['profileImage'] = "../images/noProfilePic.png";
            }
            session_start();
            $repo->signUp(new UserDTO($_POST));
            $_SESSION['loggedIn'] = true;
            $_SESSION['username'] = $un;
            header("Location: http://" . $_SERVER['HTTP_HOST'] . "/controllers/index.php");
            exit;
    }
}
require_once("../views/signUp.phtml");
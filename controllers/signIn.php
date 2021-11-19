<?php
if(session_id() == ''){
    session_start();
}
$view = new stdClass();
$view->pageTitle = "Sign in";
$view->validation = null;
require_once("../models/UsersRepo.php");
if(isset($_POST['submit'])){
    $repo = new UsersRepo();
    switch ($repo->signIn($_POST['username'], $_POST['password'])){
        case "T":
            $_SESSION['loggedIn'] = true;
            header("Location: http://" . $_SERVER['HTTP_HOST'] . "/controllers/index.php");
            exit;
        case "WU":
            $view->validation = "Wrong Username";
            break;
        case "WP":
            $view->validation = "Wrong Password";
            break;
        default:
            $view->validation = null;
            break;
    }

}
require_once("../views/signIn.phtml");
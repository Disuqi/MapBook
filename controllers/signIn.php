<?php

if(session_id() == ''){
    session_start();
}

$view = new stdClass();
$view->pageTitle = "Sign in";
$view->validation = "<br>";

require_once("../models/UsersRepo.php");
require_once("../models/Signer.php");
require_once("cookie.php");
//checks if the button submit was pressed
if(isset($_POST['submit'])){
    $repo = new UsersRepo();
    $un = $_POST['username']; //un is an abbreviation for username
    $pw = $_POST['password']; //pw is an abbreviation for password
    //checks if the password is correct
    switch ($repo->signIn($un, $pw)){
        case "T":
            //sign in if the password was correct
            $signer = new Signer();
            $signer->signIn($un);
            //stay signed in was pressed?
            if(isset($_POST['staySignedIn'])){
                // make a cookie to let the website know that the user allows cookies
                setcookie("allowCookies", "true", time() +(86400 * 365), "/");
                // make a cookie to make the user remain signed in for a month
                setcookie("username", $un, time() + (86400 * 30), "/");
            }
            //send back to the main page
            header("Location: http://" . $_SERVER['HTTP_HOST'] . "/controllers/index.php");
            exit;
        case "WU":
            $view->validation = "Wrong Username";
            break;
        case "WP":
            $view->validation = "Wrong Password";
            break;
        default:
            $view->validation = "Something Didn't Work";
            break;
    }

}
$view->staySignedIn = null;

require_once("../views/signIn.phtml");
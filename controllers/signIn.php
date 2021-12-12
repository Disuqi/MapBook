<?php

if(session_id() == ''){
    session_start();
}

$view = new stdClass();
$view->pageTitle = "Sign in";
$view->validation = null;

require_once("../models/Repo.php");
require_once("../models/UsersRepo.php");
require_once("../models/Signer.php");
require_once("cookie.php");

if(isset($_POST['submit'])){
    $repo = new UsersRepo();
    $un = $_POST['username']; //un is an abbreviation for username
    $pw = $_POST['password']; //pw is an abbreviation for password
    switch ($repo->signIn($un, $pw)){
        case "T":
            $signer = new Signer();
            $signer->signIn($un);
            //stay signed in
            if(isset($_POST['staySignedIn'])){
                setcookie("allowCookies", "true", time() +(86400 * 365), "/");
                setcookie("username", $un, time() + (86400 * 30), "/");
            }
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

//changing the stay signed in button depending on cookies permission
$view->staySignedIn = null;

if(isset($_COOKIE['allowCookies'])){
    $view->staySignedIn = '<input type="checkbox" name="staySignedIn" class="btn-check" id="btncheck1" autocomplete="off">
                              <label class="btn btn-outline-dark form-control" for="btncheck1">Stay signed in</label>';
}
else{
    $view->staySignedIn =  '<a data-toggle="tooltip" title="Allow cookies for this feature"><label class="btn btn-outline-dark form-control" data-bs-toggle="modal" data-bs-target="#cookiesModal">Stay signed in</label></a>';
}


require_once("../views/signIn.phtml");
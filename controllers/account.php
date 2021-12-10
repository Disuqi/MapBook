<?php
if(session_id() == ''){
    session_start();
}
require_once '../models/Repo.php';
require_once '../models/UsersRepo.php';
require_once '../models/ImagesRepo.php';
$view = new stdClass();
$view->pageTitle = 'Account';
$usersRepo = new UsersRepo();
$imageRepo = new ImagesRepo();

if(isset($_SESSION['loggedIn'])){
    $un = $_SESSION['username'];
    $user = $usersRepo->getObject($un);
    $profileImage = $imageRepo->getProfileImage($un)->getImagePath();
    $view->username = $un;
    $view->profileImage = $profileImage;
    $view->fullName = $user->getFullName();
    $view->email = $user->getEmail();
    $view->position = $user->getPosition();
}else{
    header("Location: http://" . $_SERVER['HTTP_HOST'] . "/controllers/signIn.php");
    exit;
}

require_once '../views/account.phtml';
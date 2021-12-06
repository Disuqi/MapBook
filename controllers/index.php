<?php
//checking if a session has already been started, if not then start one
if(session_id() == ''){
    session_start();
}
//required later in the code
require_once('../models/Signer.php');
require_once('cookie.php');

//making an empty class that will be used in the view
$view = new stdClass();
$view->pageTitle = "Home";

//checking if someone is trying to log out of delete the account
if(isset($_GET['account']) && isset($_SESSION['loggedIn'])){
    switch ($_GET['account']){
        case "signOut":
            //Siging out the account
            $signer = new Signer();
            $signer->signOut();
            break;
        case "delete":
            //Deleting Account
            //removing user from database
            require_once("../models/Repo.php");
            require_once("../models/UsersRepo.php");
            require_once("../models/ImagesRepo.php");

            $userRepo = new UsersRepo();
            $imageRepo = new ImagesRepo();
            $signer = new Signer();
            $un = $_SESSION['username']; //abbrv for username

            $imageRepo->deleteAllImagesOfUser($un);
            $userRepo->deleteObject($un);

            //deleting directory and all images inside
            require_once("../models/Images.php");
            $imageHandler = new Images();
            $imageHandler ->deleteDirectory($un);

            //Signing out
            $signer->signOut();
            break;
    }
}

//Cookies button or auto login
if(!isset($_SESSION["doNotAllowCookies"]) && !isset($_COOKIE["allowCookies"])) {
    echo '
        <div class="col-12 text-center copyright">
            <button id="cookieBtn" data-bs-toggle="modal" data-bs-target="#cookiesModal"></button>
        </div>
';
} else if(isset($_COOKIE['username'])){
    $signer = new Signer();
    $signer->signIn($_COOKIE['username']);
}

require_once('../models/Repo.php');
require_once('../models/UsersRepo.php');

//getting all the users
$repo = new UsersRepo();
$view->users = $repo->getAll();

//display the login button or account details depending on whether the user is signed in or not
if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true){
    //                    To be added
    //                    <div class="order-2 p-0 col-1">
    //                        <button style="width:50px; height: 50px; border-style: hidden; background: none">
    //                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="white" class="bi bi-people-fill" viewBox="0 0 16 16">
    //                              <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
    //                              <path fill-rule="evenodd" d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216z"/>
    //                              <path d="M4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/>
    //                            </svg>
    //                        </button>
    //                    </div>
    $view->signInBtn = '
                    <div class="order-3 p-0 col-2 text-end">
                        <div class="dropdown dropstart">
                          <button class="btn" type="button" id="account" data-bs-toggle="dropdown" aria-expanded="false" style="background-image: url(' . $_SESSION['profileImage'] . '); margin-right: 0.5vw"></button>
                          <ul class="dropdown-menu dropdown-menu-dark text-end" style="right: calc(50px + 0.4vw)" aria-labelledby="account">
                            <li><a class="dropdown-item active" href="#">Account</a></li>
                            <li><a class="dropdown-item" href="../../controllers/index.php?account=delete">Delete Account</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../../controllers/index.php?account=signOut">Sign out</a></li>
                          </ul>
                        </div>
                    </div>
                    ';
} else{

    $view->signInBtn = '
                    <div class="order-2 p-2 col-2 text-end">
                        <a href="/controllers/signIn.php">
                            <button class="btn signInBtn" type="button">Sign in</button>
                        </a>
                    </div>
                    ';
}

require_once("../views/index.phtml");

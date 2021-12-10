<?php
//display the login button or account details depending on whether the user is signed in or not
if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']){
    $view->signInBtn = '
                        <div class="d-flex justify-content-end">
                            <a href="index.php"><button class="btn friendR text-center" type="button"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                              <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                            </svg></button></a>
                            <div class="dropdown dropstart">
                              <button class="btn friendR" type="button" data-bs-toggle="dropdown" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                                  <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                  <path fill-rule="evenodd" d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216z"/>
                                  <path d="M4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/>
                                </svg></button>
                              <ul class="dropdown-menu dropdown-menu-dark text-end" style="right: calc(50px + 0.4vw)" aria-labelledby="account">
                                '. $userLister->getRequests($_SESSION['username']).'
                              </ul>
                            </div>
                            <div class="dropdown dropstart">
                              <button class="btn account" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background-image: url(' . $_SESSION['profileImage'] . '); margin-right: 0.5vw"></button>
                              <ul class="dropdown-menu dropdown-menu-dark text-end" style="right: calc(50px + 0.4vw)" aria-labelledby="account">
                                <li><a class="dropdown-item active" href="account.php">Account</a></li>
                                <li><a class="dropdown-item" href="../../controllers/index.php?account=delete">Delete Account</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../../controllers/index.php?account=signOut">Sign out</a></li>
                              </ul>
                            </div>
                        </div>
                    ';
} else{
    $view->signInBtn = '
                        <div class="btn-group">
                                <a class="btn signInBtn" href="signIn.php" >Sign In</a>
                                <a class="btn signInBtn dropdown-toggle-split" href="signUp.php">Up</a>
                        </div>
                    ';
}

//checking if someone is trying to log out of delete the account
if(isset($_GET['account']) && isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']){
    switch ($_GET['account']){
        case "signOut":
            //Siging out the account
            $signer = new Signer();
            $signer->signOut();
            break;
        case "delete":
            //Deleting Account
            //removing user from database

            $userRepo = new UsersRepo();
            $imageRepo = new ImagesRepo();
            $friendshipRepo = new FriendshipRepo();
            $signer = new Signer();
            $un = $_SESSION['username']; //abbrv for username

            //deleting all friendships from database
            $friendshipRepo->deleteAccount($un);
            //deleting Images from database
            $imageRepo->deleteAllImagesOfUser($un);
            //deleting account from database
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